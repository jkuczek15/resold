// initialize node and puppeteer configuration
const puppeteer = require('puppeteer');
const viewportOptions = {
 width: 1920,
 height: 1040
};
const debugOptions = {
  headless: false,
  slowMo: 25,
  args: [`--window-size=${viewportOptions.width},${viewportOptions.height}`]
};

// facebook authentication config
const fb_email = 'joe.kuczek@gmail.com';
const resold_fb_phone = '8473419324';
const resold_fb_password = 'ResoldFB420';

// link selector config
const sell_button_selector = '._54qk';
const item_for_sale_selector = '._4e31';
const next_button_selector = '._1mf7._4jy1';

// input selector config
const product_name_selector = 'input[placeholder="What are you selling?"]';
const price_selector = 'input[placeholder="Price"]';
const location_selector = 'input[placeholder="Add Location"]';
const photo_selector = 'input[type=file]';
const description_selector = 'div[contenteditable=true]';
const category_selector = 'input[placeholder="Select a Category"]';

// form parameters
// these will be the input parameters required for submission
let product_name = 'Acoustic Guitar';
let product_url = 'https://resold.us/Acoustic_Guitar';
let description = `Nice acoustic guitar used about 10 times. Can ship or meet locally. To purchase this item please visit ${product_url}`;
let price = '75';
let category = 'Music';
let location = 'South Elgin, IL';

// begin our browser automation
(async () => {
  // launch a new browser instance of puppeteer
  const browser = await puppeteer.launch(debugOptions)
  const [page] = await browser.pages();
  await page.setViewport(viewportOptions);

  // override the default notification settings
  const context = browser.defaultBrowserContext()
  await context.overridePermissions('https://www.facebook.com/marketplace', ['notifications'])

  // login to Facebook
  await page.goto('https://www.facebook.com')
  await page.type('#email', fb_email)
  await page.type('#pass', fb_pass)
  await page.click('input[type=submit]')

  // go to the marketplace
  await page.goto('https://www.facebook.com/marketplace')
  await page.waitForSelector(sell_button_selector)

  // click the sell button
  await page.click(sell_button_selector)
  await page.waitForSelector(item_for_sale_selector)
  await page.click(item_for_sale_selector)

  // start filling out the sell form
  // fill out the item name
  await page.waitForSelector(product_name_selector)
  await page.type(product_name_selector, product_name)

  // fill out the price
  await page.waitForSelector(price_selector)
  await page.type(price_selector, price)

  // fill out the location
  await page.waitForSelector(location_selector)
  await page.click(location_selector, {clickCount: 3})
  await page.type(location_selector, location)
  await page.waitFor(250)
  await page.keyboard.press('Tab')

  // fill out the category
  await page.waitForSelector(category_selector)
  await page.type(category_selector, category)
  await page.keyboard.press('ArrowDown')
  await page.keyboard.press('Tab')

  // fill out the description
  await page.waitForSelector(description_selector)
  await page.type(description_selector, description)

  // add photos of the item
  await page.waitForSelector(photo_selector)
  await page.click(photo_selector)
  const photoInput = await page.$(photo_selector)
  await photoInput.uploadFile('./item_images/guitar.jpeg')

  // move onto the next step
  await page.waitForSelector(next_button_selector)
  await page.waitFor(1000)
  await page.click(next_button_selector)

  // post the item for sale
  await page.waitForSelector(next_button_selector)
  await page.waitFor(1000)
  await page.click(next_button_selector)

  // await browser.close();
})();
