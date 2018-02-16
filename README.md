# OSF high-level pdf generator component

High-level pdf documents (letters, invoices) generator. Based on TCPDF for the moment.

## Installation

You need at least php 7.1 and `composer`:

```bash
sudo apt install composer
```

### In your application via composer

This is the recommended way to use this feature in a non-osf project.

Just add `osflab/pdf` in your composer.json file.

### From github

To test the component or participate in its development.

```bash
git clone https://github.com/osflab/pdf.git
cd pdf && composer update
```

Unit tests launch:

```bash
vendor/bin/runtests
```
