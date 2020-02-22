const fs = require("fs");
const path = require("path");

let filter = (arr) => {
  return arr.filter(function (el) {
    return el != null && el.trim() != "";
  });
};

const posts = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/posts-list.txt`), "utf8")
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

const emailLinkIncludes = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-link-include.txt`), "utf8")
  .split("\n"));

const emailNames = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-closing-names.txt`), "utf8")
  .split("\n"));

const emailSubjects = filter(fs
  .readFileSync(path.join(__dirname, "./", `resources/email-subjects.txt`), "utf8")
  .split("\n"));

const resold_url = "https://resold.us"

module.exports = {
  resold_url,
  posts,
  emailSubjects,
  emailStarters,
  emailBodys,
  emailLinkIncludes,
  emailClosers,
  emailNames
};
