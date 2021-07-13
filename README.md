# Resold
DoorDash, but for anything. Resold is a powerful delivery integration that uses Stripe Connect and Postmates APIs.

[![alt text](https://embed.filekitcdn.com/e/sKJvWa4mhcgjAyC9zVb3wu/5B4zu3rNXt2o7hXWdPQX2A)](https://resold.us){:target="_blank"}

# Web Stack
- Linux
- Apache Version 2.4.29
- MySQL Version 5.6
- PHP Version 7.1.3
- Magento Community Edition 2.2

# Delivery App Mobile Stack
- Flutter / Dart

# AWS Cloud
- EC2 Web Server instances with ELB
- MySQL RDS instance (ready to upgrade to Aurora for scalability)
- CloudFront distribution pointing to ELB
- ElasticSearch Engine for product indexing
- Kibana search dashboard for viewing product information
- Code, CodeDeploy, and CodePipeline + automated build scripts
- Lambda function for automated Puppeteer scripts
- Route53 for DNS pointing to Cloudfront
- S3 bucket for static assets and images

To host Resold on your local machine, reference SETUP.md.
