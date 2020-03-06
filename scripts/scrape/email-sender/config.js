const fs = require("fs");
const path = require("path");

let filter = (arr) => {
  return arr.filter(function (el) {
    return el != null && el.trim() != "";
  });
};

// flags
const debug = true;
const sendGrid = true;
const createAccounts = false;

// constants
const debug_email = '6ac975ed0ef536b3a4ebae2754b3f74d@sale.craigslist.org';
const from_email = 'sales@resold.us';
const resold_url = "https://resold.us";
const read_retries = 10000;
const send_limit = 10000;

// list file resources
const posts = filter(fs
  .readFileSync(path.join(__dirname, "./", `db/posts-list.txt`), "utf8")
  .split("\n"));

const emailAccounts = filter(fs
  .readFileSync(path.join(__dirname, "./", `db/email-accounts.txt`), "utf8")
  .split("\n"));

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
  .readFileSync(path.join(__dirname, "./", `resources/email-closing-names.txt`), "utf8")
  .split("\n"));

const emailSubjects = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-subjects.txt`), "utf8")
  .split("\n"));

const emailLinkIncludes = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-link-includes.txt`), "utf8")
  .split("\n"));

const emailZeroFee = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-zero-fee.txt`), "utf8")
  .split("\n"));

const emailResoldZeroFee = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-resold-zero-fee.txt`), "utf8")
  .split("\n"));

const emailSecureCashless = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-secure-cashless.txt`), "utf8")
  .split("\n"));

module.exports = {
  resold_url,
  read_retries,
  send_limit,
  from_email,
  debug_email,
  debug,
  sendGrid,
  createAccounts,
  posts,
  emailAccounts,
  emailSubjects,
  emailStarters,
  emailBodys,
  emailClosers,
  emailNames,
  emailLinkIncludes,
  emailZeroFee,
  emailResoldZeroFee,
  emailSecureCashless
};
