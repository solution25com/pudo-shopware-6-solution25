[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/solution25com/pudo-shopware-6-solution25/blob/main/LICENSE)

# PUDO Point for Shopware 6

## Introduction

The **PUDO Point Plugin** enables customers to select a nearby PUDO pickup location as their shipping destination directly within your Shopware 6 checkout. Instead of shipping to a home address, customers choose a convenient local PUDO dealer — and all dealer details are automatically stored on the order for seamless fulfillment.

This plugin integrates into the Shopware shipping method selection, fetches real-time dealer availability via the PUDO API, and gives merchants full visibility of the selected pickup point in the Admin order view.

---

## Key Features

### PUDO Shipping Method
- Adds a dedicated **Shipping PUDO** option to the checkout shipping step.

### Real-Time Dealer Lookup
- Fetches nearby PUDO Point locations based on the customer's **billing zip code** via the PUDO API.

### Dealer Distance Filtering
- Filters results by a configurable **maximum distance** and displays up to 10 nearby dealers.

### Order Custom Fields
- Saves the full selected dealer profile to the order, including address, coordinates, phone, hours, and more.

### Automatic Shipping Address Update
- Replaces the delivery address on the order with the chosen **PUDO dealer address** upon checkout.

### B2B Exclusion Support
- Configure specific **customer groups** (e.g. wholesale/B2B) for which the PUDO option is hidden.

### Admin Panel Integration
- View the selected PUDO Point dealer details directly in the **Shopware Admin order view**.

### Fully Configurable
- All API credentials and behavior settings are managed via the Shopware **System Config** panel.

---

## Compatibility
- ✅ Shopware 6.6.x

---

## Get Started

### Installation & Activation

#### GitHub

1. Clone the plugin into your Shopware plugins directory:

```bash
git clone https://github.com/solution25com/pudo-shopware-6-solution25.git
```

2. **Install the Plugin in Shopware 6**

   - Log in to your Shopware 6 Administration panel.
   - Navigate to **Extensions > My Extensions**.
   - Locate the plugin and click **Install**.

3. **Activate the Plugin**

   - After installation, click **Activate** to enable the plugin.
   - Run the following commands from your Shopware root:

```bash
bin/console plugin:refresh
bin/console plugin:install --activate Pudo
bin/console cache:clear
```

4. **Build Storefront Assets**

```bash
bin/console bundle:dump
bin/build-storefront.sh
bin/console cache:clear
```

5. **Verify Installation**

   - After activation, you will see **Pudo Point** in the list of installed plugins.
   - The plugin name, version, and installation date should appear.

---

## Plugin Configuration

After installing the plugin, configure your **PUDO** credentials and options through the Shopware Administration panel.

### Accessing the Configuration

1. Go to **Settings > Extensions > Pudo Point**
2. Select the **Sales Channel** you want to configure
3. Set the following fields:

### API Credentials

| Field | Description |
|---|---|
| **Signature** | API signature sent as the `x-signature` request header |
| **Base URI** | Base URL of the PUDO API (e.g. `https://api.pudo.com`) |
| **API Endpoint** | Endpoint path for dealer lookup (e.g. `/v1/dealers`) |
| **Partner Code** | Your PUDO partner code |
| **Partner Password** | Your PUDO partner password |

### Additional Settings

| Field | Description |
|---|---|
| **Maximum Dealer Distance** | Maximum distance used to filter nearby dealers (in the unit defined by the PUDO API) |
| **B2B Customer Groups** | Select customer groups for which the PUDO shipping option should be deactivated |

---

## Checkout Experience

The plugin integrates seamlessly into the Shopware 6 checkout, offering a smooth and intuitive shipping selection process.

### 1. Selecting the Shipping Method (PUDO)

On the checkout page, the customer selects the shipping option labelled **Shipping PUDO**. Once selected, a dropdown of nearby PUDO Point dealers appears, allowing the customer to choose their preferred pickup location.

### 2. Confirming the Order

After selecting a preferred PUDO Point dealer, the customer completes the standard checkout process and submits the order.

### 3. Viewing the Order in the Admin Panel

In **Admin > Orders**, the order now reflects the selected PUDO Point dealer. The shipping address on the order is automatically updated to the dealer's address.

### 4. Viewing Order Details

Opening the order details in the Admin displays all relevant PUDO Point dealer information saved as order custom fields, including:

- Dealer ID, name, and number
- Full address (street, city, province, postal code, country)
- Coordinates (latitude / longitude)
- Phone number, opening hours, and supported languages
- Distance from the customer's billing address
- 24/7 availability flag

---
