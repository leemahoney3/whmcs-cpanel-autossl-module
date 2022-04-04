# cPanel AutoSSL Module for WHMCS

Create an SSL Certificate addon that utilizes cPanel's AutoSSL feature for free SSL Certificates.

This is still a work in progress, I hope to make it more functional in the future.

For this to work your cPanel users need to be on a feature list where the AutoSSL feature is disabled.

When the addon is activated it overrides the cPanel user's feature list to enable AutoSSL and runs a check. When the addon is suspended or terminated, the overridden feature is removed.

## How to install

1. Copy the ```modules``` folder to your root WHMCS directory.

2. Create a new Product Addon in WHMCS, choose **Independent Product** and select **cPanel AutoSSL** from the Module list.
3. Set up the rest of the addon as normal and enjoy.



## Contributions

Feel free to fork the repo, make changes, then create a pull request! For ideas on what you can help with, check the project issues.