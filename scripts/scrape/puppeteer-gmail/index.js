const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const pluginStealth = require("puppeteer-extra-plugin-stealth");
const chalk = require("chalk");
const debug = true;
const debugEmail = 'joe.kuczek@gmail.com';
let config = require("./config");

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

  let userIndex = 0;
  let user = config.emailAccounts[userIndex];
  let page = await emailSender.login(puppeteer, user);

  let retry = 0;
  let emailsSent = 0;

  // loop to retry reading from last sent email index
  while(retry++ < config.read_retries) {
    let lastEmailIndex = await emailSender.getLastSentEmailIndex();
    config = require('./config');

    // loop over all craigslist posts
    for (let i = lastEmailIndex; i < config.posts.length; i++) {
      // check if we've reached our gmail send limit for this account
      if(emailsSent+1 == config.send_limit){
        // close the browser and login with a new account
        await page.close();
        user = config.emailAccounts[++userIndex];
        page = await emailSender.login(puppeteer, user);
      }// end if send limit has been reached

      // retreive the data for the next post
      let post = config.posts[i].split(",");
      let email = post[0];
      let title = post[1].replace(/&amp;/g, '&');
      let queryString = post[2];

      // check if we are in debug mode
      if(debug){
        email = debugEmail;
      }// end if debug mode

      // build an email dynamically from random strings in resources folder
      let subject = getRandom(config.emailSubjects);
      let greeting = getRandom(config.emailStarters);
      let body = templateReplace(getRandom(config.emailBodys), "{title}", title);
      let closer = getRandom(config.emailClosers);
      let name = getRandom(config.emailNames);
      let linkInclude = getRandom(config.emailLinkIncludes);
      let zeroFee = getRandom(config.emailZeroFee);
      let resoldZeroFee = getRandom(config.emailResoldZeroFee);
      let secureCashless = getRandom(config.emailSecureCashless);

      // email contents
      let url = `${config.resold_url}/sell${queryString}`;
      let message = `${greeting}\r\n${body} ${secureCashless}\r\n${zeroFee} ${resoldZeroFee}\r\n`;
      let closing = `\r\n${closer}\r\n${name}`;

      try {
        await emailSender.writeNewEmail(page, {
          index: i,
          subject,
          email,
          message,
          url,
          closing,
          linkInclude
        });
        emailsSent++;
      } catch(err) {
        // there was an error trying to send the email
        console.log(chalk.red.inverse(`Error sending email to: ${email}. Exception is: ${err.message}`));
        page = await emailSender.login(puppeteer, user);
      }// end try-catch writing a new email

      if(debug) { break; }
    }// end for loop over posts

    if(debug) { break; }
  }// end while loop for retries

  await browser.close();
})();
