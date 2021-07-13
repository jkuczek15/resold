const aws = require('aws-sdk');
const s3 = new aws.S3({apiVersion: '2006-03-01'});
const fs = require('fs');
const tar = require('tar');
const puppeteer = require('puppeteer');
const config = require('./config');
const chromium = require('chrome-aws-lambda');

exports.getBrowser = (() => {
  let browser;
  return async () => {
    if (typeof browser === 'undefined' || !await isBrowserAvailable(browser)) {
      await setupChrome();
      browser = await puppeteer.launch({
        headless: chromium.headless,
        executablePath: await chromium.executablePath,
        defaultViewport: chromium.defaultViewport,
        args: config.launchOptionForLambda,
        dumpio: !!exports.DEBUG,
      });
      debugLog(async (b) => `launch done: ${await browser.version()}`);
    }
    return browser;
  };
})();

let viewportOptions = {
   width: 1920,
   height: 1040,
};
exports.config = {
  // basic settings
  viewportOptions,
  debugOptions: {
    headless: false,
    slowMo: 100,
    args: [`--window-size=${viewportOptions.width},${viewportOptions.height}`],
  },

  // facebook authentication settings
  fb_email: '<your Facebook email>',
  fb_pass: '<your Facebook phone number>',
  resold_fb_phone: '<your Facebook phone number>',
  resold_fb_password: '<your Facebook password>',

  // link selector config
  checkpoint_selector: '#checkpointSubmitButton',
  sell_button_selector: '._54qk',
  item_for_sale_selector: '._4e31',
  next_button_selector: '._1mf7._4jy1',

  // input selector config
  product_name_selector: 'input[placeholder="What are you selling?"]',
  price_selector: 'input[placeholder="Price"]',
  location_selector: 'input[placeholder="Add Location"]',
  photo_selector: 'input[type=file]',
  description_selector: 'div[contenteditable=true]',
  category_selector: 'input[placeholder="Select a Category"]',
};

const isBrowserAvailable = async (browser) => {
  try {
    await browser.version();
  } catch (e) {
    debugLog(e); // not opened etc.
    return false;
  }
  return true;
};

const setupChrome = async () => {
  if (!await existsExecutableChrome()) {
    if (await existsLocalChrome()) {
      debugLog('setup local chrome');
      await setupLocalChrome();
    } else {
      debugLog('setup s3 chrome');
      await setupS3Chrome();
    }
    debugLog('setup done');
  }
};

const existsLocalChrome = () => {
  return new Promise((resolve, reject) => {
    fs.exists(config.localChromePath, (exists) => {
      resolve(exists);
    });
  });
};

const existsExecutableChrome = () => {
  return new Promise((resolve, reject) => {
    fs.exists(config.executablePath, (exists) => {
      resolve(exists);
    });
  });
};

const setupLocalChrome = () => {
  return new Promise((resolve, reject) => {
    fs.createReadStream(config.localChromePath)
    .on('error', (err) => reject(err))
    .pipe(tar.x({
      C: config.setupChromePath,
    }))
    .on('error', (err) => reject(err))
    .on('end', () => resolve());
  });
};

const setupS3Chrome = () => {
  return new Promise((resolve, reject) => {
    const params = {
      Bucket: config.remoteChromeS3Bucket,
      Key: config.remoteChromeS3Key,
    };
    s3.getObject(params)
    .createReadStream()
    .on('error', (err) => reject(err))
    .pipe(tar.x({
      C: config.setupChromePath,
    }))
    .on('error', (err) => reject(err))
    .on('end', () => resolve());
  });
};

const debugLog = (log) => {
  if (config.DEBUG) {
    let message = log;
    if (typeof log === 'function') message = log();
    Promise.resolve(message).then(
      (message) => console.log(message)
    );
  }
};
