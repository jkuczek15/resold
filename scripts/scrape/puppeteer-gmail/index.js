const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const pluginStealth = require("puppeteer-extra-plugin-stealth");
let config = require("./config");
const debug = false;
const debugEmail = '93eb7fa0513b373f8a51d3b4c4cf7869@sale.craigslist.org';

let getRandom = (arr) => {
  return arr[Math.floor(Math.random()*arr.length)];
};

let templateReplace = (phrase, key, value) => {
  if(phrase.includes(key)){
    return phrase.replace(key, value).replace(/['"]+/g, '').trim();
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

  const page = (await browser.pages())[0];
  await emailSender.login(page);

  let retry = 0;
  while(retry++ < config.read_retries) {
    let lastEmailIndex = await emailSender.getLastSentEmailIndex();
    config = require('./config');

    for (let i = lastEmailIndex; i < config.posts.length; i++) {
      let post = config.posts[i].split(",");

      let email = post[0];
      let title = post[1].replace(/&amp;/g, '&');
      let queryString = post[2];

      if(debug){
        email = debugEmail;
      }

      let subject = getRandom(config.emailSubjects);
      let greeting = getRandom(config.emailStarters);
      let body = templateReplace(getRandom(config.emailBodys), "{title}", title);
      let closer = getRandom(config.emailClosers);
      let name = getRandom(config.emailNames);
      let linkInclude = getRandom(config.emailLinkIncludes);
      let zeroFee = getRandom(config.emailZeroFee);
      let resoldZeroFee = getRandom(config.emailResoldZeroFee);

      let url = `${config.resold_url}/sell${queryString}`;
      let message = `${greeting}\r\n${body}\r\n${zeroFee} ${resoldZeroFee}\r\n`;
      let closing = `\r\n${closer}\r\n${name}`;

      await emailSender.writeNewEmail(page, {
        index: i,
        subject,
        email,
        message,
        url,
        closing,
        linkInclude
      });
    }// end for loop over posts
  }// end while loop for retries

  await browser.close();
})();
