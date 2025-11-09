import puppeteer from 'puppeteer';
import dotenv from 'dotenv';

process.env.DOTENVX_QUIET = 'true';
dotenv.config();

const checkoutId = process.argv[2];
const firstName = process.argv[3];
const lastName = process.argv[4];
const email = process.argv[5];
const phone = process.argv[6];
const phonecode = process.argv[7];
const cardNumber = process.argv[8];
const cardMonth = process.argv[9];
const cardYear = process.argv[10];
const securityCode = process.argv[11];
const connectionURL = process.argv[12];

const store = process.env.STORE || 'tuplace';
const storeDomain = `${store}.mycartpanda.com`;

const getCsrfToken = `document.querySelector('meta[name="csrf-token"]').getAttribute('content')`;
const getCartToken = `getCookie('cart_token')`;

const states = [
  { code: 'AL', name: 'Alabama', cities: ['Birmingham', 'Montgomery', 'Mobile'] },
  { code: 'AZ', name: 'Arizona', cities: ['Phoenix', 'Tucson', 'Mesa'] },
  { code: 'CA', name: 'California', cities: ['Los Angeles', 'San Diego', 'San Jose'] },
  { code: 'CO', name: 'Colorado', cities: ['Denver', 'Colorado Springs', 'Aurora'] },
  { code: 'FL', name: 'Florida', cities: ['Miami', 'Orlando', 'Tampa'] },
  { code: 'GA', name: 'Georgia', cities: ['Atlanta', 'Savannah', 'Augusta'] },
  { code: 'IL', name: 'Illinois', cities: ['Chicago', 'Springfield', 'Naperville'] },
  { code: 'MA', name: 'Massachusetts', cities: ['Boston', 'Cambridge', 'Springfield'] },
  { code: 'NC', name: 'North Carolina', cities: ['Charlotte', 'Raleigh', 'Durham'] },
  { code: 'NJ', name: 'New Jersey', cities: ['Newark', 'Jersey City', 'Trenton'] },
  { code: 'NY', name: 'New York', cities: ['New York', 'Buffalo', 'Rochester'] },
  { code: 'OH', name: 'Ohio', cities: ['Columbus', 'Cleveland', 'Cincinnati'] },
  { code: 'PA', name: 'Pennsylvania', cities: ['Philadelphia', 'Pittsburgh', 'Allentown'] },
  { code: 'TX', name: 'Texas', cities: ['Houston', 'Dallas', 'Austin'] },
  { code: 'VA', name: 'Virginia', cities: ['Richmond', 'Virginia Beach', 'Alexandria'] },
  { code: 'WA', name: 'Washington', cities: ['Seattle', 'Tacoma', 'Spokane'] }
];

const streetNames = [
  'Maple Street',
  'Oak Avenue',
  'Pine Road',
  'Cedar Lane',
  'Elm Street',
  'Washington Boulevard',
  'Jefferson Street',
  'Madison Avenue',
  'Franklin Road',
  'Lincoln Street'
];

function getRandomItem(list) {
  return list[Math.floor(Math.random() * list.length)];
}

function padZip(zip) {
  return zip.toString().padStart(5, '0');
}

function generateUsAddress() {
  const state = getRandomItem(states);
  const city = getRandomItem(state.cities);
  const street = getRandomItem(streetNames);
  const number = Math.floor(Math.random() * 8999) + 100;
  const zipcode = padZip(Math.floor(Math.random() * 99999));
  const neighborhood = 'Downtown';
  const apartment =
    Math.random() > 0.7 ? `Apt ${Math.floor(Math.random() * 900) + 100}` : '';

  return {
    street,
    number: number.toString(),
    city,
    stateCode: state.code,
    stateName: state.name,
    zipcode,
    neighborhood,
    apartment,
    country: 'United States'
  };
}

const fetchAbandoned = `(async () => {
  const response = await fetch("https://${storeDomain}/abandoned", {
    "headers": {
      "accept": "*/*",
      "accept-language": "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7,zh-CN;q=0.6,zh;q=0.5",
      "checkout-country": "US",
      "checkout-currency": "USD",
      "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
      "priority": "u=1, i",
      "sec-fetch-dest": "empty",
      "sec-fetch-mode": "cors",
      "sec-fetch-site": "same-origin",
      "x-csrf-token": "{{csrftoken}}",
      "x-requested-with": "XMLHttpRequest"
    },
    "referrer": "https://${storeDomain}/checkout",
    "referrerPolicy": "strict-origin-when-cross-origin",
    "body": "customer%5Bemail%5D={{email}}&customer%5Bstate_reg_num%5D=&customer%5BphoneNumber%5D={{phone}}&customer%5Bphonecode%5D=%2B{{phonecode}}&customer%5BfirstName%5D={{firstname}}&customer%5BlastName%5D={{lastname}}&customer%5Baddress%5D={{address}}&customer%5Bzipcode%5D={{zipcode}}&customer%5Bcity%5D={{city}}&customer%5Bstate%5D={{state}}&customer%5Bcountry%5D=United%20States&cartToken={{cartToken}}&country_code=US",
    "method": "POST",
    "mode": "cors",
    "credentials": "include"
  });
  return await response.json();
})()`;

const fetchGatewayPayment = `(async () => {
  const response = await fetch("https://${storeDomain}/gatewaypay", {
    "headers": {
      "accept": "*/*",
      "accept-language": "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7,zh-CN;q=0.6,zh;q=0.5",
      "content-type": "application/json",
      "priority": "u=1, i",
      "sec-fetch-dest": "empty",
      "sec-fetch-mode": "cors",
      "sec-fetch-site": "cross-site"
    },
    "referrer": "https://${storeDomain}/",
    "referrerPolicy": "strict-origin-when-cross-origin",
    "body": JSON.stringify({
      "current_route": "checkout",
      "cartpay_checkout": "0",
      "cartpay_enabled": "0",
      "cart_country_code": "US",
      "is_global_market": "1",
      "checkout_request_currency": "USD",
      "cartTotalWeight": "1800",
      "checkoutSubTotalPrice": "29.99",
      "checkoutTotalPrice": "34.98",
      "checkoutTotalPriceGlobal": "34.98",
      "totalShippingPrice": "4.99",
      "totalShippingPriceGlobal": "4.99",
      "totalTax": "0",
      "totalDiscount": "0.00",
      "include_shipping_amount": "1",
      "discount_category": "",
      "discountCode": "0",
      "giftDiscountPrice": "0",
      "giftDiscountCode": "0",
      "shipping_gateway": "standard",
      "melhor_envio_service": "0",
      "melhor_envio_company": "0",
      "melhor_envio_packages": "0",
      "paid_by_client": "1",
      "custom_price": "0",
      "digital_cart_items": "0",
      "country_code": "US",
      "ocu_exists": "0",
      "browser_ip": document.querySelector('input[name="browser_ip"]').getAttribute('value'),
      "quantity": "1",
      "couponCode": "",
      "email": "{{email}}",
      "fullName": "{{fullname}}",
      "phoneNumber": "{{phone}}",
      "phonecode": "{{phonecode}}",
      "ficalNumber": "",
      "cnpjNumber": "",
      "registrationNumber": "",
      "zipcode": "{{zipcode}}",
      "city": "{{city}}",
      "state": "{{state}}",
      "address": "{{address}}",
      "number": "{{number}}",
      "neighborhood": "{{neighborhood}}",
      "compartment": "{{compartment}}",
      "country": "{{country}}",
      "cardNumber": "{{cardnumber}}",
      "cardholderName": "{{fullname}}",
      "cardExpiryDate": "{{cardmonth}}/{{cardyear}}",
      "securityCode": "{{securitycode}}",
      "installments": "1",
      "save_information": "1",
      "ebanking": "",
      "docType": "",
      "docNumber": "",
      "site_id": "MLB",
      "cardExpirationMonth": "{{cardmonth}}",
      "cardExpirationYear": "{{cardyear}}",
      "paymentMethodId": "cc",
      "recover_source": "",
      "alert_message_product_qty_not_available": "Sorry, this product is out of stock.",
      "alert_message_cart_is_empty": "Your cart is empty.",
      "sayswho": "a",
      "addCCDiscountPrice": "0",
      "paymentMethod": "cc",
      "payment_type": "cartpanda_stripe",
      "payment_token": "upnid",
      "visitorID": "lalf",
      "cart_token": "{{carttoken}}",
      "abandoned_token": "null",
      "currency": "USD"
    }),
    "method": "POST",
  });
  return await response.json();
})()`;

(async () => {
  const browser = await puppeteer.connect({
    browserWSEndpoint: connectionURL,
  });

  const page = await browser.newPage();
  await page.setRequestInterception(true);
  page.on('console', (msg) => {
    try {
      console.error(`[page-console:${msg.type()}]`, msg.text());
    } catch {
      console.error('[page-console]', msg.text());
    }
  });
  page.on('pageerror', (error) => {
    console.error('[page-error]', error?.stack || error?.message || error);
  });
  page.on('requestfailed', (request) => {
    console.error('[request-failed]', {
      url: request.url(),
      method: request.method(),
      failure: request.failure(),
    });
  });
  page.on('response', async (response) => {
    try {
      const url = response.url();
      if (url.includes('/gatewaypay') || url.includes('/abandoned')) {
        const status = response.status();
        const body = await response.clone().text();
        console.error('[response]', { url, status, body });
      }
    } catch (error) {
      console.error('[response-log-error]', error?.stack || error?.message || error);
    }
  });
  page.on('request', (req) => {
    const resourceType = req.resourceType();
    if (['image', 'stylesheet', 'font', 'media'].includes(resourceType)) {
      req.abort();
    } else {
      req.continue();
    }
  });

  await page.goto(`https://${storeDomain}/checkout/${checkoutId}`, {
    waitUntil: 'domcontentloaded',
    timeout: 120000,
  });
  await page.waitForSelector('input[name="browser_ip"]', { timeout: 120000 });

  const csrfToken = await page.evaluate(getCsrfToken);
  const cartToken = await page.evaluate(getCartToken);
  const address = generateUsAddress();

  const replacedFetchAbandoned = fetchAbandoned
    .replace('{{csrftoken}}', csrfToken)
    .replace('{{cartToken}}', cartToken)
    .replace('{{email}}', email)
    .replace('{{phone}}', phone)
    .replace('{{phonecode}}', phonecode)
    .replace('{{firstname}}', firstName)
    .replace('{{lastname}}', lastName)
    .replace('{{address}}', encodeURIComponent(`${address.number} ${address.street}`))
    .replace('{{zipcode}}', address.zipcode)
    .replace('{{city}}', encodeURIComponent(address.city))
    .replace('{{state}}', address.stateCode);

  let abandonedResponse;
  try {
    abandonedResponse = await page.evaluate(replacedFetchAbandoned);
  } catch (error) {
    console.error('[abandoned-evaluate-error]', error?.stack || error?.message || error);
    throw error;
  }
  if (abandonedResponse) {
    console.error('[abandoned-result]', JSON.stringify(abandonedResponse));
  } else {
    console.error('[abandoned-result-empty]');
  }

  const replacedFetchGatewayPayment = fetchGatewayPayment
    .replaceAll('{{email}}', email)
    .replaceAll('{{fullname}}', `${firstName} ${lastName}`)
    .replaceAll('{{phone}}', phone)
    .replaceAll('{{phonecode}}', phonecode)
    .replaceAll('{{cardnumber}}', cardNumber)
    .replaceAll('{{cardmonth}}', cardMonth)
    .replaceAll('{{cardyear}}', cardYear)
    .replaceAll('{{securitycode}}', securityCode)
    .replaceAll('{{carttoken}}', cartToken)
    .replaceAll('{{zipcode}}', address.zipcode)
    .replaceAll('{{city}}', address.city)
    .replaceAll('{{state}}', address.stateCode)
    .replaceAll('{{address}}', `${address.number} ${address.street}`)
    .replaceAll('{{number}}', address.number)
    .replaceAll('{{neighborhood}}', address.neighborhood)
    .replaceAll('{{compartment}}', address.apartment)
    .replaceAll('{{country}}', address.country);

  let gatewayPayment;
  try {
    gatewayPayment = await page.evaluate(replacedFetchGatewayPayment);
  } catch (error) {
    console.error('[gatewaypay-evaluate-error]', error?.stack || error?.message || error);
    throw error;
  }

  if (!gatewayPayment) {
    console.error('[gatewaypay-empty-response]');
  } else if (gatewayPayment.success === false || gatewayPayment.error) {
    console.error('[gatewaypay-declined]', gatewayPayment);
  }
  console.error('[gatewaypay-result]', JSON.stringify(gatewayPayment ?? {}));

  console.log(JSON.stringify(gatewayPayment));

  await browser.close();
})();

