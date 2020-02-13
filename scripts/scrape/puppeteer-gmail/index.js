const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const fs = require("fs");
const path = require("path");
const pluginStealth = require("puppeteer-extra-plugin-stealth");

const posts = fs
  .readFileSync(path.join(__dirname, "./", `resources/posts-list.txt`), "utf8")
  .split("\n");

/* Customizable variables */
const subject = "Your listing on Craigslist";
const message =
`Hello,
I hope you're having a great day.
Kind regards,
Joe.`;

(async () => {
  puppeteer.use(pluginStealth());
  const browser = await puppeteer.launch({
    headless: false,
    timeout: 0
  });

  const lastEmailIndex = await emailSender.getLastSentEmailIndex();

  const page = await browser.newPage();
  await emailSender.login(page);

  for (let i = lastEmailIndex; i < posts.length; i++) {

    let post = posts[i].split(",");

    // todo: dynamically build email message based on this stuff + random emails
    let email = post[0];
    let title = post[1];
    let price = post[2];
    let location = post[3];
    let timeago = post[4];
    let url = post[5];

    await emailSender.writeNewEmail(page, {
      index: i,
      subject,
      email,
      message
    });
  }

  await browser.close();
})();
