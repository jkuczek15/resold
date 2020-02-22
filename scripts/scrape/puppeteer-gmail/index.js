const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const config = require("./config");
const pluginStealth = require("puppeteer-extra-plugin-stealth");

let getRandom = (arr) => {
  return arr[Math.floor(Math.random()*arr.length)];
};

let templateReplace = (phrase, key, value) => {
  if(phrase.includes(key)){
    return phrase.replace(key, value).replace("\"", "");
  }else{
    return phrase;
  }
};

(async () => {
  puppeteer.use(pluginStealth());
  const browser = await puppeteer.launch({
    headless: false,
    timeout: 0
  });

  const lastEmailIndex = await emailSender.getLastSentEmailIndex();
  const page = await browser.newPage();
  await emailSender.login(page);

  for (let i = lastEmailIndex; i < config.posts.length; i++) {
    let post = config.posts[i].split(",");

    let email = post[0];
    let title = post[1];

    let subject = getRandom(config.emailSubjects);
    let greeting = getRandom(config.emailStarters);
    let body = templateReplace(getRandom(config.emailBodys), "{title}", title);
    let linkInclude = templateReplace(getRandom(config.emailLinkIncludes), "{url}", config.resold_url);
    let closing = getRandom(config.emailClosers);
    let name = getRandom(config.emailNames);

    let message = `${greeting}\r\n${body} ${linkInclude}\r\n${closing}\r\n${name}`;

    await emailSender.writeNewEmail(page, {
      index: i,
      subject,
      email,
      message
    });
  }

  await browser.close();
})();
