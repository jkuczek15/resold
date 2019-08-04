const setup = require('./starter-kit/setup');

module.exports.handler = async (event, context, callback) => {
  // For keeping the browser launch
  context.callbackWaitsForEmptyEventLoop = false;
  const browser = await setup.getBrowser();
  try {
    const result = await exports.run(browser);
    callback(null, result);
  } catch (e) {
    callback(e);
  }
};

// form parameters
// these will be the input parameters required for submission
let productName = 'Acoustic Guitar';
let productUrl = 'https://resold.us/Acoustic_Guitar';
let description = `Nice acoustic guitar used about 10 times. Can ship or meet locally. To purchase this item please visit ${productUrl}`;
let price = '75';
let category = 'Music';
let location = 'South Elgin, IL';

exports.run = async (browser) => {
  // implement here
  const page = await browser.newPage();
  await page.setViewport(setup.config.viewportOptions);

  // override the default notification settings
  const context = browser.defaultBrowserContext();
  await context.overridePermissions('https://www.facebook.com/marketplace', ['notifications']);

  // login to Facebook
  await page.goto('https://www.facebook.com');

  // check where we are
  // one of three places, login screen, logged in screen, or verification checkpoint
  let selector = await Promise.race([
    page.waitForSelector('#email'),
    page.waitForSelector('#creation_hub_entrypoint'),
    page.waitForSelector(setup.config.checkpoint_selector)
  ]);

  let selectorDesc = selector._remoteObject.description;
  if(selectorDesc.includes('#email')){
    // email field exists and we are not signed in
    await page.type('#email', setup.config.fb_email);
    await page.type('#pass', setup.config.fb_pass);
    await page.click('input[type=submit]');

    // check if we hit the checkpoint or if we are logged in
    selector = await Promise.race([
      page.waitForSelector('#creation_hub_entrypoint'),
      page.waitForSelector(setup.config.checkpoint_selector)
    ]);
    selectorDesc = selector._remoteObject.description;

    // check returned value to see if we hit the checkpoint
    if(selectorDesc.includes(setup.config.checkpoint_selector)){
      // we hit the checkpoint
      await page.click(setup.config.checkpoint_selector);
      await page.waitForNavigation();
    }// end if we hit the checkpoint after logging in

  }else if(selectorDesc.includes(setup.config.checkpoint_selector)){
    // we are already logged in and were hit with a checkpoint
    await page.click(setup.config.checkpoint_selector);
    await page.waitForNavigation();
  }// end if we need to login

  // go to the marketplace
  await page.goto('https://www.facebook.com/marketplace');

  // click the sell button
  await page.waitForSelector(setup.config.sell_button_selector);
  await page.click(setup.config.sell_button_selector);
  await page.waitForSelector(setup.config.item_for_sale_selector);
  await page.click(setup.config.item_for_sale_selector);

  // start filling out the sell form
  // fill out the item name
  await page.waitForSelector(setup.config.product_name_selector);
  await page.type(setup.config.product_name_selector, productName);

  // fill out the price
  await page.waitForSelector(setup.config.price_selector);
  await page.type(setup.config.price_selector, price);

  // fill out the location
  await page.waitForSelector(setup.config.location_selector);
  await page.click(setup.config.location_selector, {clickCount: 3});
  await page.type(setup.config.location_selector, location);
  await page.waitFor(500);
  await page.keyboard.press('Tab');

  // fill out the description
  await page.waitForSelector(setup.config.description_selector);
  await page.type(setup.config.description_selector, description);

  // add photos of the item
  await page.waitForSelector(setup.config.photo_selector);
  await page.click(setup.config.photo_selector);
  const photoInput = await page.$(setup.config.photo_selector);
  await photoInput.uploadFile('./item_images/guitar.jpeg');

  // fill out the category
  await page.waitForSelector(setup.config.category_selector);
  await page.type(setup.config.category_selector, category);
  await page.keyboard.press('ArrowDown');
  await page.keyboard.press('Tab');

  // move onto the next step
  await page.waitForSelector(setup.config.next_button_selector);
  await page.waitFor(1250);
  await page.click(setup.config.next_button_selector);

  // post the item for sale
  await page.waitForSelector(setup.config.next_button_selector);
  await page.waitFor(1250);

  await page.click(setup.config.next_button_selector);
  await page.waitFor(5000);
  await page.close();
  return 'done';
};
