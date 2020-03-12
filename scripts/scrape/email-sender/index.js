const puppeteer = require("puppeteer-extra");
const emailSender = require("./email-sender");
const pluginStealth = require("puppeteer-extra-plugin-stealth");
const chalk = require("chalk");
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
  if(!config.sendGrid){
    puppeteer.use(pluginStealth());
  }// end if not using sendgrid

  let userIndex = 0;
  let retry = 0;
  let emailsSent = 0;
  let user = config.emailAccounts[userIndex];

  if(config.createAccounts){
    for(let i = 0; i < config.emailAccounts.length; i++){
      let user = config.emailAccounts[i];
      await emailSender.createAccount(puppeteer, user);
    }// end for loop attempting to create gmail accounts
  }// end if we want to create the gmail accounts

  if(!config.sendGrid){
    let page = await emailSender.login(puppeteer, user);
  }// end if we aren't using sendgrid

  // loop to retry reading from last sent email index
  while(true) {
    let lastEmailIndex = await emailSender.getLastSentEmailIndex();
    config.posts = config.getLatestPosts();

    // loop over all craigslist posts
    for (let i = lastEmailIndex+1; i < config.posts.length; i++) {
      // check if we've reached our gmail send limit for this account
      if(!config.sendGrid && emailsSent+1 == config.send_limit){
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
      if(config.debug){
        email = config.debug_email;
      }// end if debug mode

      // build an email dynamically from random strings in resources folder
      let subject = getRandom(config.emailSubjects);
      let greeting = getRandom(config.emailStarters);
      let body = getRandom(config.emailBodys);
      let resoldBody = templateReplace(getRandom(config.emailBodysResold), "{title}", title);
      let closer = getRandom(config.emailClosers);
      let linkInclude = getRandom(config.emailLinkIncludes);
      let fromUser = getRandom(config.emailFrom);
      let fromParts = fromUser.split(',');
      let fromName = fromParts[0];
      let fromEmail = getRandom(config.emailFromResold);

      // build one-click url
      let baseUrl = config.debug ? config.debug_url : config.resold_url;
      let url = `${baseUrl}/sell${queryString}`;

      // send the email
      try {
        if(config.sendGrid){
          let html = `${greeting}
          <br/><br/>
          ${body} ${resoldBody}
          <br/><br/>
          <a href="${url}">${linkInclude}</a>
          <br/><br/>
          ${closer}
          <br/><br/>
          ${fromName}`;
          await emailSender.writeNewEmailSendGrid(i, email, fromEmail, fromName, subject, html, config.useSMTPRelay);
        }else{
          let message = `${greeting}\r\n${body}\r\n`;
          let closing = `\r\n${closer}\r\n${fromName}`;
          await emailSender.writeNewEmail(page, {
            index: i,
            subject,
            email,
            message,
            url,
            closing,
            linkInclude
          });
        }// end if using sendgrid to send emails
        if(emailsSent++ > config.send_limit) { break; }
      } catch(err) {
        // there was an error trying to send the email
        console.log(chalk.red.inverse(`Error sending email to: ${email}. Exception is: ${err.message}`));
        page = await emailSender.login(puppeteer, user);
      }// end try-catch writing a new email

      if(config.debug) { break; }
    }// end for loop over posts

    if(emailsSent > config.send_limit) { break; }
    if(config.debug) { break; }
  }// end while loop for retries

  if(!config.sendGrid){
    await browser.close();
  }// end if not using sendgrid

  console.log(chalk.green.inverse(`Total emails sent: ${emailsSent}.`));
})();
