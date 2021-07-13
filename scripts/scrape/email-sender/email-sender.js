require("dotenv").config();

const chalk = require("chalk");
const path = require("path");
const fs = require("fs").promises;
const atob = require('atob');
const sendgrid = require('@sendgrid/mail');
const nodemailer = require("nodemailer");
const htmlToText = require('html-to-text');

// email configuration
sendgrid.setApiKey(process.env.SENDGRID_API_KEY);

// sendgrid smtp
// let transporter = nodemailer.createTransport({
//   host: 'smtp.sendgrid.net',
//   port: 587,
//   secure: false,
//   auth: {
//     user: 'apikey',
//     pass: process.env.SENDGRID_API_KEY
//   }
// });

// custom smtp
let transporter = nodemailer.createTransport({
  host: '<your mail host>',
  port: 587,
  secure: false,
  auth: {
    user: '<your email>',
    pass: '<your password>'
  }
});

const lastEmailIndex = path.join(
  __dirname,
  "./",
  "db/",
  "last-sent-email-index.txt"
);

/* Customizable variables */
const phoneNumber = '8473419324';
const delayBetweenEmails = 35;
const delayBetweenSteps = 150;
const emailTypeDelay = 25;
const subjectTypeDelay = 75;
const bodyTypeDelay = 50;

const emailSender = {
  createAccount: async (puppeteer, user) => {
    console.log(chalk.whiteBright.inverse("Creating Gmail account..."));

    let userParts = user.split(':');
    let userName = userParts[0];
    let password = userParts[1];
    let userNameSplit = userName.split('@');
    userName = userNameSplit[0];

    const browser = await puppeteer.launch({
      headless: false,
      timeout: 0
    });
    const page = (await browser.pages())[0];
    await page.goto(
      "https://accounts.google.com/signup/v2/webcreateaccount?flowName=GlifWebSignIn&flowEntry=SignUp",
      {timeout: 0}
    );

    // first name
    await page.waitForSelector(`#firstName`);
    await page.type(`#firstName`, 'Resold', { delay: 35 });

    // last name
    await page.waitForSelector(`#lastName`);
    await page.type(`#lastName`, 'Listings', { delay: 35 });

    // username
    await page.waitForSelector(`#username`);
    await page.type(`#username`, userName, { delay: 35 });

    // password
    await page.waitForSelector(`input[type='password']`);
    await page.type(`input[type='password']`, password, { delay: 35 });

    // confirm password
    await page.waitForSelector(`input[name='ConfirmPasswd']`);
    await page.type(`input[name='ConfirmPasswd']`, password, { delay: 35 });
    await page.keyboard.press("Enter");

    // wait for navigation
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"], {timeout: 0});
    await page.waitFor(3550);

    // enter phone number
    await page.waitForSelector(`#phoneNumberId`);
    await page.type(`#phoneNumberId`, phoneNumber, { delay: 35 });
    await page.keyboard.press("Enter");

    // wait for navigation
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"], {timeout: 0});
    await page.waitFor(3550);

    // wait for verification code to be entered
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"], {timeout: 0});
    await page.waitFor(3550);

    // enter month
    await page.waitForSelector(`#month`);
    await page.type(`#month`, 'October', { delay: 35 });
    await page.keyboard.press("Enter");

    // wait for navigation
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"], {timeout: 0});
    await page.waitFor(3550);

    console.log(chalk.whiteBright.inverse("Account created succesfully."));
    await page.waitFor(5000);

    return page;
  },
  login: async (puppeteer, user) => {
    console.log(chalk.whiteBright.inverse("Logging in Gmail..."));

    let userParts = user.split(':');
    let userName = userParts[0];
    let password = userParts[1];

    const browser = await puppeteer.launch({
      headless: false,
      timeout: 0
    });
    const page = (await browser.pages())[0];
    await page.goto(
      "https://accounts.google.com/AccountChooser?service=mail&continue=https://mail.google.com/mail/",
      {timeout: 0}
    );

    // email
    await page.waitForSelector(`input[type='email']`);
    await page.type(`input[type='email']`, userName, { delay: 35 });
    await page.keyboard.press("Enter");

    // wait delay
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"]);
    await page.waitFor(3550);

    // password
    await page.waitForSelector(`input[type='password']`);
    await page.type(`input[type='password']`, password, { delay: 35 });
    await page.keyboard.press("Enter");
    await page.waitForNavigation(["networkidle0", "load", "domcontentloaded"]);

    console.log(chalk.whiteBright.inverse("Logged in succesfully."));
    await page.waitFor(5000);

    return page;
  },
  writeNewEmail: async (page, { index, subject, email, message, url, closing, linkInclude }) => {
    console.log(chalk.whiteBright.inverse(`${index}. Writing new e-mail...`));

    const $newEmailButton = `[jscontroller] > [id] > [class] > [id] div[style][role='button'][class]`;
    const $emailInput = `textarea[name = "to"]`;
    const $subjectInput = `input[name='subjectbox']`;
    const $messageInput = `[aria-label*='Message Body'][role=textbox]`;
    const $emailIsBeingSent = `[aria-live="assertive"] > div > div:nth-child(2) > span > span:nth-child(1)`;
    const $linkTextInput = '#linkdialog-text';
    const $linkInput = '#linkdialog-onweb-tab-input';
    const $linkOkButton = 'button[name="ok"]';

    await page.waitForSelector($newEmailButton);
    await page.click($newEmailButton);

    // type out email
    await page.waitForSelector($emailInput);
    await page.type($emailInput, email, {delay: emailTypeDelay});
    await page.waitFor(delayBetweenSteps);

    // type out subject
    await page.waitForSelector($subjectInput);
    await page.type($subjectInput, subject, {delay: subjectTypeDelay });
    await page.waitFor(delayBetweenSteps);

    // type out body
    await page.waitForSelector($messageInput);
    await page.click($messageInput);
    await page.waitFor(500);
    await page.type($messageInput, message, {delay: bodyTypeDelay });
    await page.waitFor(delayBetweenSteps);

    // add a link element to one-click post on Resold
    await page.keyboard.down('Control');
    await page.keyboard.press('KeyK');
    await page.keyboard.up('Control');
    await page.waitForSelector($linkTextInput);
    await page.type($linkTextInput, linkInclude, {delay: bodyTypeDelay});
    await page.$eval($linkInput, (el, value) => {
      el.value = value;
      document.querySelector('button[name="ok"]').disabled = false;
    }, url);
    await page.click($linkOkButton);
    await page.waitFor(delayBetweenSteps);

    // type out closing
    await page.waitFor(500);
    await page.keyboard.type(closing, {delay: bodyTypeDelay});
    await page.waitFor(delayBetweenSteps);

    // send the email
    await page.keyboard.press("Tab");
    await page.waitFor(delayBetweenSteps);
    await page.keyboard.press("Enter");
    await page.waitFor(delayBetweenSteps);
    await page.waitForSelector($emailIsBeingSent);

    try {
      emailSender.saveLastSentEmailIndex(index);
      console.log(`${chalk.whiteBright(email)} finished.`);
    } catch (error) {
      console.log(`${Number(index)}. Couldn't check if e-mail was delivered.`);
    }

    await page.waitFor(delayBetweenEmails);
  },
  writeNewEmailSendGrid: async (index, to_email, from_email, from_name, subject, html, useSMTPRelay, debug) => {
    try {
      console.log(chalk.whiteBright.inverse(`${index}. Writing new e-mail...`));
      let text = htmlToText.fromString(html);
      if(useSMTPRelay){
        // send mail with sendgrid smtp relay
        let info = await transporter.sendMail({
          from: `${from_name} <${from_email}>`,
          to: to_email,
          subject: subject,
          html: html,
          text: text
        });
      }else{
        // send mail with sendgrid web api
        const msg = {
          to: to_email,
          from: {
            name: from_name,
            email: from_email
          },
          subject: subject,
          html: html,
          text: text
        };
        sendgrid.send(msg);
      }// end if using the SMTP relay
    }catch (error) {
      console.log(`${Number(index)}. Couldn't send email with error: ${error.message}.`);
    }// end try-catch writing email

    if(!debug){
      try {
        emailSender.saveLastSentEmailIndex(index);
      } catch (error) {
        console.log(`${Number(index)}. Couldn't check if e-mail was delivered.`);
      }
    }// end if not debugging

    console.log(`${chalk.whiteBright(to_email)} finished.`);
    await sleep(delayBetweenEmails);
  },
  getLastSentEmailIndex: async _ => {
    try {
      const index = await fs.readFile(lastEmailIndex, "utf8");
      return Number(index);
    } catch (err) {
      console.error(err);
    }
  },
  saveLastSentEmailIndex: async index => {
    try {
      await fs.writeFile(lastEmailIndex, index);
      console.log(chalk.green("Updated index succesfully."));
    } catch (err) {
      console.error(err);
    }
  }
};

const sleep = (ms) => {
  return new Promise(resolve => setTimeout(resolve, ms));
};

module.exports = emailSender;
