const fs = require("fs");
const path = require("path");

let filter = (arr) => {
  return arr.filter(function (el) {
    return el != null && el.trim() != "";
  });
};

// flags
const debug = false;
const sendGrid = true;
const createAccounts = false;
const useSMTPRelay = true;

// constants
const debug_email = '9c93777b41c23c8bb7a29254132721ec@sale.craigslist.org';
const resold_url = 'https://resold.us';
const read_retries = 1;
const send_limit = 100000;

// list file resources
const posts = filter(fs
  .readFileSync(path.join(__dirname, "./", `db/posts-list.txt`), "utf8")
  .split("\n"));

const emailAccounts = filter(fs
  .readFileSync(path.join(__dirname, "./", `db/email-accounts.txt`), "utf8")
  .split("\n"));

const emailSubjects = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-subjects.txt`), "utf8")
  .split("\n"));

const emailStarters = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-starters.txt`), "utf8")
  .split("\n"));

const emailFrom = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-from.txt`), "utf8")
  .split("\n"));

const emailFromResold = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-from-resold.txt`), "utf8")
  .split("\n"));

const emailBodys = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-body.txt`), "utf8")
  .split("\n"));

const emailBodysResold = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-body-resold.txt`), "utf8")
  .split("\n"));

const emailLinkIncludes = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-link-includes.txt`), "utf8")
  .split("\n"));

const emailClosers = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-closers.txt`), "utf8")
  .split("\n"));

module.exports = {
  resold_url,
  read_retries,
  send_limit,
  debug_email,
  debug,
  sendGrid,
  useSMTPRelay,
  createAccounts,
  posts,
  emailAccounts,
  emailSubjects,
  emailStarters,
  emailBodys,
  emailBodysResold,
  emailClosers,
  emailFrom,
  emailFromResold,
  emailLinkIncludes
};
