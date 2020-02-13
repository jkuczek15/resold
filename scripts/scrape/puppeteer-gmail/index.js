const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const fs = require("fs");
const path = require("path");
const pluginStealth = require("puppeteer-extra-plugin-stealth");

let getRandom = (arr) => {
  return arr[Math.floor(Math.random()*arr.length)];
};

let filter = (arr) => {
  return arr.filter(function (el) {
    return el != null;
  });
};

const posts = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/posts-list.txt`), "utf8")
  .split("\n"));

/* Customizable variables */
const emailStarters = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-starters.txt`), "utf8")
  .split("\n"));

const emailBodys = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-body.txt`), "utf8")
  .split("\n"));

const emailClosers = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-closers.txt`), "utf8")
  .split("\n"));

const emailNames = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-names.txt`), "utf8")
  .split("\n"));

const emailSubjects = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-subjects.txt`), "utf8")
  .split("\n"));

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

    // todo: dynamically build email message based on this stuff
    let email = post[0];
    // let title = post[1];
    // let price = post[2];
    // let location = post[3];
    // let timeago = post[4];
    // let url = post[5];

    let subject = getRandom(emailSubjects);
    let greeting = getRandom(emailStarters);
    let body = getRandom(emailBodys);
    let closing = getRandom(emailClosers);
    let name = getRandom(emailNames);
    let message = `${greeting}\r\n${body}\r\n${closing}\r\n${name}`;

    await emailSender.writeNewEmail(page, {
      index: i,
      subject,
      email,
      message
    });
  }

  await browser.close();
})();
