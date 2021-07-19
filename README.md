# Resold
DoorDash, for anything. Resold is a powerful delivery integration that uses Stripe Connect and Postmates APIs.

<a href="https://resold.us" target="_blank">
  <img src="https://embed.filekitcdn.com/e/sKJvWa4mhcgjAyC9zVb3wu/5B4zu3rNXt2o7hXWdPQX2A"></img>
</a>

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
- ElastiCache in-memory Redis cache
- Code, CodeDeploy, and CodePipeline + automated build scripts
- Lambda function for automated Puppeteer scripts
- Route53 for DNS pointing to Cloudfront
- S3 bucket for static assets and images

# Google Cloud
- Firebase database for real-time messaging
- Google maps integration

# Third-party APIs
- Stripe Connect
- Postmates
- Mapbox
- Twilio
- SendGrid

To host Resold on your local machine, clone the repo and reference SETUP.md.
