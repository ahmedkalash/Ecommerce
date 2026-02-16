# **Domain Decomposition and Architectural Refactoring Strategy for Active eCommerce CMS**

The technical architecture of modern enterprise-level eCommerce platforms must balance the rigors of high-concurrency
transactional integrity with the flexibility required for rapid feature deployment. The Active eCommerce CMS, a
Laravel-based multi-vendor marketplace solution, represents a complex monolith that has evolved through multiple
framework iterations, currently supporting features ranging from physical product sales and digital downloads to auction
systems and point-of-sale (POS) integrations.1 This report provides an exhaustive domain decomposition of the system,
identifying the technical debt inherent in its current structure and proposing a transition toward a modular,
domain-driven architecture. By isolating business logic from the delivery mechanism and strictly adhering to SOLID
principles, the refactored system will achieve higher testability, security, and scalability.5

## **Authentication and User Management Module**

The Authentication and User Management domain serves as the foundational security layer for the entire marketplace
ecosystem. Within the Active eCommerce framework, this module is responsible for identifying and authorizing a diverse
range of actors, each with distinct operational scopes and data access requirements. This domain is not merely a
gatekeeper for session management but acts as the central authority for role-based access control (RBAC), staff
management, and seller onboarding.4

### **High-Level Business Logic**

The primary purpose of this module is the lifecycle management of digital identities and their associated privileges. It
creates the trust environment necessary for financial transactions to occur. In a multi-vendor context, the logic must
handle complex actor hierarchies where a single user may transition between roles or maintain separate profiles for
different activities.10

| Actor            | Primary Business Responsibility                                                                          |
|:-----------------|:---------------------------------------------------------------------------------------------------------|
| **Admin**        | System governance, staff role assignment, seller verification, and marketplace-wide policy enforcement.3 |
| **Seller**       | Shop management, inventory control, and financial withdrawal requests.4                                  |
| **Customer**     | Profile management, order tracking, wallet top-ups, and review submission.2                              |
| **Delivery Boy** | Fulfillment status updates, earnings tracking, and assigned delivery logistics.4                         |

### **Low-Level Technical Mapping**

The current implementation of user management in Active eCommerce often suffers from the "God Model" anti-pattern, where
the User model handles auth, profile data, wallet balances, and seller-specific configurations. This results in high
coupling and violates the Single Responsibility Principle (SRP).14

| Component                 | Technical Detail                                                                                               |
|:--------------------------|:---------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Models\\User, App\\Http\\Controllers\\UserController, App\\Http\\Controllers\\Auth\\RegisterController.14 |
| **Tables Involved**       | users, staffs, roles, permissions, sellers, shops, customers.8                                                 |
| **Primary Relationships** | User hasOne Seller; User hasOne Customer; User belongsTo Role; Role hasMany Permissions.7                      |
| **Critical Files**        | app/Http/Middleware/IsAdmin.php, app/Models/Role.php, app/Http/Controllers/Api/Auth/LoginController.php.12     |

The impact of refactoring this module is high, as the User entity is a primary foreign key in nearly every major table,
including orders, carts, and products. The current code exhibits significant code smell through deeply nested
conditionals in controllers to check user types rather than using polymorphic relations or specialized guards.4

### **Execution Plan**

| Step                     | Task Description                                                                                                                |
|:-------------------------|:--------------------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Analyze App\\Models\\User to identify non-auth methods (e.g., get\_user\_orders) that should be moved to Repositories.4         |
| **1\. Data Integrity**   | Migrate any floating-point columns in the users table related to financial data (e.g., balance) from double to decimal(15,2).20 |
| **2\. Logic Extraction** | Implement an IdentityService to handle multi-actor registration and social auth (Google, Facebook, Twitter).8                   |
| **3\. API Hardening**    | Enforce UUIDs for user public identifiers and audit all profile-update endpoints for IDOR vulnerabilities.21                    |

### **Hidden Risks and Security Surface**

Refactoring the authentication domain carries the risk of invalidating existing user sessions and breaking third-party
add-ons like the "OTP" or "Google One-Tap Login" modules.4 Security surfaces are critical; common vulnerabilities in
this module include SQL injection in user search filters and Insecure Direct Object References (IDOR) where a customer
might guess an incremental ID to access another user's dashboard or order history.21

## **Product Catalog and Merchandising Module**

The Product Catalog is the central data repository for the marketplace's saleable items. It encompasses a wide variety
of product types, including physical goods, digital downloads, classified ads, and auction items.2 This module must
manage complex taxonomies, attribute sets, and variant-based pricing while maintaining a high-performance search
experience.

### **High-Level Business Logic**

The product domain facilitates the discovery and evaluation of goods. It transforms raw manufacturer data into
localized, marketing-optimized listings. In a multi-vendor environment, this module must also handle seller-specific
product approval workflows and catalog isolation.7

| Component         | Business Logic Overview                                                                         |
|:------------------|:------------------------------------------------------------------------------------------------|
| **Taxonomy**      | hierarchical categorization and brand management to aid navigation.7                            |
| **Attributes**    | Dynamic property management (size, color, weight) to support filtering and variant generation.7 |
| **Digital Goods** | Management of file uploads, license keys, and download limits.2                                 |
| **Auction Logic** | Time-sensitive bidding cycles and bid history tracking.1                                        |

### **Low-Level Technical Mapping**

The Product domain is currently characterized by heavy controller logic and a sprawling products table that uses JSON
columns for attributes, which can complicate SQL-based filtering and reporting.7

| Component                 | Technical Detail                                                                                                         |
|:--------------------------|:-------------------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\ProductController, App\\Http\\Controllers\\Admin\\ProductController.4                            |
| **Tables Involved**       | products, categories, brands, attributes, attribute\_values, product\_stocks, flash\_deals.4                             |
| **Primary Relationships** | Product belongsTo Category; Product hasMany ProductStock; Product hasMany Reviews.4                                      |
| **Likely Laravel Files**  | app/Models/Product.php, app/Http/Resources/ProductCollection.php, app/Services/ProductSearchService.php (Recommended).12 |

Dependency mapping indicates that changes to the products table will ripple through the "Flash Deals," "Coupons," and "
Wholesale" modules.1 The module exhibits high cyclomatic complexity in its variation-selection logic, which often
happens directly within the controller or view.1

### **Execution Plan**

| Step                     | Task Description                                                                                            |
|:-------------------------|:------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Map all product-type variations (Auction, Digital, Classified) to identify shared vs. unique attributes.3   |
| **1\. Data Integrity**   | Standardize the product\_stocks table to ensure stock quantities and prices are handled as decimal types.20 |
| **2\. Logic Extraction** | Move variation-price calculation and attribute-filtering logic into a ProductCatalogService.19              |
| **3\. API Hardening**    | Sanitize all search inputs and implement rate-limiting for the "Suggestive Search" feature.3                |

### **Hidden Risks and Security Surface**

Refactoring the product catalog can inadvertently break SEO URLs or existing marketing campaigns if slug generation
logic is altered.8 The security surface includes "Insecure File Uploads" in product image management and XSS
vulnerabilities in product description fields if they are not properly sanitized before rendering in the frontend.21

## **Order Management and Fulfillment Lifecycle**

Order Management is the engine of the eCommerce platform, governing the transition of a buyer's intent into a completed
commercial transaction. This domain is responsible for tracking the immutable state of a purchase, including price
snapshots, tax calculations, and multi-stage delivery statuses.4

### **High-Level Business Logic**

The order module acts as the legal and financial record of the sale. It manages the coordination between customers,
sellers, and delivery personnel. In a marketplace setting, it must also handle "Order Splitting," where a single
customer checkout results in multiple sub-orders for different vendors.4

| Stage           | Business Logic Responsibility                                                    |
|:----------------|:---------------------------------------------------------------------------------|
| **Placement**   | Validation of cart state, price locking, and initial ledger entry.4              |
| **Processing**  | Seller notification, stock reservation, and payment verification.4               |
| **Fulfillment** | Coordination with the "Delivery Boy" module or 3PL providers like Shiprocket.1   |
| **Completion**  | Finalizing commissions, updating seller wallets, and enabling customer reviews.4 |

### **Low-Level Technical Mapping**

Order logic is currently heavily coupled with the CheckoutController, leading to a "God Controller" that handles cart
clearing, payment redirection, and order notification in a single procedural flow.4

| Component                 | Technical Detail                                                                             |
|:--------------------------|:---------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\OrderController, App\\Http\\Controllers\\CheckoutController.4        |
| **Tables Involved**       | orders, order\_details, order\_status\_histories, payments, wallets.4                        |
| **Primary Relationships** | Order hasMany OrderDetails; Order belongsTo User; Order hasOne Payment.4                     |
| **Critical Files**        | app/Events/OrderPlaced.php, app/Listeners/SendOrderNotifications.php, app/Models/Order.php.4 |

The system requires a clear extraction boundary between the "Transactional State" (Order Placement) and the "Operational
State" (Logistics and Fulfillment). Error handling gaps are often found in the payment callback logic, where an
interrupted request can leave an order in an inconsistent state.20

### **Execution Plan**

| Step                     | Task Description                                                                                                                   |
|:-------------------------|:-----------------------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Audit all order status transitions to identify hard-coded status strings in controllers.4                                          |
| **1\. Data Integrity**   | Ensure the order\_details table stores the "price at purchase" as a decimal(15,2) rather than a reference to the products table.20 |
| **2\. Logic Extraction** | Implement a WorkflowService or State Machine to manage order status transitions (Pending \-\> Confirmed \-\> Shipped).4            |
| **3\. API Hardening**    | Implement strict IDOR checks to ensure only the authorized buyer, seller, or admin can access order details.21                     |

### **Hidden Risks and Security Surface**

Refactoring orders can disrupt the "Wallet" and "Affiliate" systems, which rely on order completion events to distribute
funds.4 Security vulnerabilities include "Price Manipulation" at checkout and the risk of "Race Conditions" during
inventory reduction if not handled within a database transaction.20

## **Payment Orchestration and Financial Gateways**

The Payment module is the most sensitive domain in the platform, managing the interface between the eCommerce
application and external financial providers. It supports a diverse range of payment methods, including international
gateways like PayPal and Stripe, as well as regional methods like bKash and Nagad.7

### **High-Level Business Logic**

The primary purpose of this module is to ensure the secure, idempotent processing of financial transactions. It must
handle authorization, capture, and settlement while providing a consistent interface for the application to interact
with various third-party APIs.20

| Payment Type         | Business Rule Application                                                |
|:---------------------|:-------------------------------------------------------------------------|
| **Online Gateways**  | Real-time verification, webhook handling, and refund management.7        |
| **Offline Payments** | Admin verification of bank slips or manual transaction IDs.8             |
| **Wallet Payments**  | Internal ledger balance checks and immediate debiting.4                  |
| **COD**              | Payment status update upon delivery confirmation by the "Delivery Boy".4 |

### **Low-Level Technical Mapping**

The current architecture features dozens of individual controllers for each payment gateway (e.g., PaypalController,
StripeController), leading to massive code duplication and making it difficult to maintain a unified payment
experience.20

| Component                 | Technical Detail                                                                                        |
|:--------------------------|:--------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\PaymentController, App\\Http\\Controllers\\CheckoutController.4                 |
| **Tables Involved**       | payments, wallets, wallet\_transactions, combined\_orders.4                                             |
| **Primary Relationships** | Payment belongsTo Order; WalletTransaction belongsTo User.4                                             |
| **Likely Laravel Files**  | app/Services/PaymentGatewayInterface.php (Needs Creation), app/Http/Controllers/PaypalController.php.20 |

Refactoring requires the introduction of a "Gateway Strategy" pattern, where the application interacts with a
PaymentService that abstracts away the specifics of each provider.20

### **Execution Plan**

| Step                     | Task Description                                                                                           |
|:-------------------------|:-----------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Identify common logic across all gateway controllers (e.g., redirection, success/fail handling).20         |
| **1\. Data Integrity**   | Audit the wallet\_transactions table to ensure that all financial amounts are stored as decimal(15,2).20   |
| **2\. Logic Extraction** | Extract gateway-specific logic into a PaymentProviders namespace using an Interface for standardization.20 |
| **3\. API Hardening**    | Implement HMAC signature verification for all webhooks and protect against "Replay Attacks".20             |

### **Hidden Risks and Security Surface**

The highest risk is the "Double Capture" or "Lost Transaction" scenario, which occurs if the application state is not
correctly synchronized with the gateway callback. Security vulnerabilities include the exposure of API secrets in the
business\_settings table (if not properly masked) and "Parameter Tampering" during the checkout redirection process.20

## **Multi-Vendor Ecosystem and Seller Management**

The Multi-Vendor module distinguishes Active eCommerce as a marketplace solution rather than a simple storefront. It
manages the isolation of seller data, shop-specific branding, and the financial relationship between the platform and
its merchants.7

### **High-Level Business Logic**

This module governs the onboarding and operational scope of third-party merchants. It facilitates the "
Store-within-a-Store" concept, allowing sellers to manage their own products, branding, and fulfillment while adhering
to platform-wide standards.3

| Business Domain   | Responsibility                                                                 |
|:------------------|:-------------------------------------------------------------------------------|
| **Onboarding**    | Registration, verification requests, and administrative approval.4             |
| **Monetization**  | Subscription packages and tiered commission structures.7                       |
| **Shop Branding** | Management of banners, social links, and store-specific landing pages.3        |
| **Payouts**       | Handling withdrawal requests and tracking administrative payments to sellers.4 |

### **Low-Level Technical Mapping**

The seller logic is currently distributed between SellerController and ShopController, leading to fragmented management
of seller identities and their associated store assets.4

| Component                 | Technical Detail                                                                                               |
|:--------------------------|:---------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\SellerController, App\\Http\\Controllers\\Admin\\SellerController.4                    |
| **Tables Involved**       | sellers, shops, seller\_payouts, seller\_withdraw\_requests, seller\_packages.4                                |
| **Primary Relationships** | User hasOne Seller; Seller hasOne Shop; Shop hasMany Products.4                                                |
| **Critical Files**        | app/Models/Seller.php, app/Http/Middleware/IsSeller.php, app/Http/Controllers/Seller/DashboardController.php.4 |

The coupling between Seller and Order is significant, as the system must calculate commissions dynamically for every
item sold. Refactoring requires isolating this "Commission Logic" into a standalone service.7

### **Execution Plan**

| Step                     | Task Description                                                                                                        |
|:-------------------------|:------------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Review the "Seller Subscription" logic to see how it restricts product listings and category access.17                  |
| **1\. Data Integrity**   | Ensure the seller\_payouts table maintains a strictly auditable record of all fund transfers.7                          |
| **2\. Logic Extraction** | Implement a SellerService to handle shop branding and verification workflows.19                                         |
| **3\. API Hardening**    | Audit all seller-end APIs for IDOR to ensure sellers cannot access each other's sales reports or withdrawal requests.21 |

### **Hidden Risks and Security Surface**

Refactoring this domain may impact the "Follow Seller" and "Product Inquiry" features, which rely on stable seller IDs.4
Security risks include "Privilege Escalation" where a seller might attempt to access admin-level functionalities and "
Social Engineering" through the seller-to-customer messaging system.4

## **Cart and Checkout Experience Management**

The Cart and Checkout module is the primary interface for customer conversion. It manages the stateful interaction of
item selection and the multi-step process of transitioning from a visitor to a buyer.4

### **High-Level Business Logic**

The cart domain is responsible for aggregating customer intent while accounting for dynamic factors like stock
availability, real-time pricing updates, and multi-vendor shipping constraints.4

| Feature               | Business Logic Requirement                                                                  |
|:----------------------|:--------------------------------------------------------------------------------------------|
| **Cart Persistence**  | Seamless transition between guest sessions and authenticated user accounts.4                |
| **Price Calculation** | Real-time application of flash deals, category discounts, and coupon codes.1                |
| **Checkout Wizard**   | managing the sequence of Address Selection \-\> Shipping Method \-\> Payment Confirmation.4 |
| **Tax & VAT**         | Calculating localized taxes based on the shipping destination.7                             |

### **Low-Level Technical Mapping**

The Cart logic is currently shared between the web-based CartController and the Api/CartController used by the Flutter
apps, often leading to divergent business rules between platforms.4

| Component                 | Technical Detail                                                                                            |
|:--------------------------|:------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\CartController, App\\Http\\Controllers\\CheckoutController.4                        |
| **Tables Involved**       | carts, cart\_items, addresses, shipping\_configurations.4                                                   |
| **Primary Relationships** | User hasMany CartItems; CartItem belongsTo Product; Cart belongsTo User.4                                   |
| **Critical Files**        | app/Http/Resources/CartResource.php, app/Models/Cart.php, app/Http/Controllers/Api/V2/CartController.php.12 |

The extraction boundary should focus on a CartService that provides a unified API for both the Web and Mobile delivery
layers, ensuring consistent price and stock validation.19

### **Execution Plan**

| Step                     | Task Description                                                                                                      |
|:-------------------------|:----------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Audit how "Multi-Seller Checkout" handles shipping cost aggregation across different vendors.4                        |
| **1\. Data Integrity**   | Implement a "Cart Expiry" logic to release reserved inventory for inactive sessions.                                  |
| **2\. Logic Extraction** | Move tax and shipping estimation logic from controllers to a PriceCalculatorService.19                                |
| **3\. API Hardening**    | Protect the checkout endpoints against "Inventory Brute-forcing" and ensure address IDs belong to the current user.21 |

### **Hidden Risks and Security Surface**

Refactoring the checkout flow can break "Abandoned Cart" notifications and "Coupon" application triggers.4 Security
surfaces include "Insecure Mass Assignment" when updating customer shipping addresses and the risk of session hijacking
in guest-checkout flows.21

## **Pricing, Coupons, and Promotional Logic**

Marketing automation within Active eCommerce is handled by the Pricing and Promotions module. This domain is responsible
for the complex overlapping of discounts, flash deals, and coupon-based incentives.1

### **High-Level Business Logic**

The goal of this module is to drive sales volume through time-sensitive or condition-based price reductions. The logic
must define the "Priority of Discounts" to ensure the platform remains profitable.4

| Discount Type   | Logic Application                                                                  |
|:----------------|:-----------------------------------------------------------------------------------|
| **Flash Deals** | Time-locked global promotions with custom banners and timers.1                     |
| **Coupons**     | Code-based discounts restricted by minimum spend, specific users, or categories.4  |
| **Wholesale**   | Tiered pricing based on quantity (B2B features).1                                  |
| **Club Points** | Reward-based currency earned through purchases and convertible to wallet balance.8 |

### **Low-Level Technical Mapping**

The promotional logic is currently scattered across ProductController (for display) and CheckoutController (for
application), making it difficult to audit the total discount applied to an order.4

| Component                 | Technical Detail                                                                             |
|:--------------------------|:---------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\FlashDealController, App\\Http\\Controllers\\CouponController.1      |
| **Tables Involved**       | flash\_deals, flash\_deal\_products, coupons, coupon\_usages, club\_points.4                 |
| **Primary Relationships** | FlashDeal hasMany Product; Coupon belongsTo Seller (optional); User hasMany ClubPoints.4     |
| **Critical Files**        | app/Http/Middleware/CheckCoupon.php, app/Models/FlashDeal.php, app/Helpers/PriceHelper.php.4 |

Extraction boundaries should involve a PricingEngine that accepts a Product and User and returns the "Current Effective
Price" after all applicable discounts.19

### **Execution Plan**

| Step                     | Task Description                                                                                              |
|:-------------------------|:--------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Identify if coupons can stack with flash deals and define the conflict-resolution rules.1                     |
| **1\. Data Integrity**   | Ensure all discount and price columns in marketing tables are migrated to decimal(15,2).20                    |
| **2\. Logic Extraction** | Implement a DiscountService to handle the validation and redemption of coupons and points.19                  |
| **3\. API Hardening**    | Protect coupon application endpoints from "Brute Force" guessing attacks and rate-limit reward redemptions.21 |

### **Hidden Risks and Security Surface**

Refactoring this domain may lead to "Rounding Errors" in total order calculations if the precision is not strictly
handled. Security risks include "Race Conditions" where a user might apply a single-use coupon multiple times across
concurrent sessions before the usage count is incremented.21

## **Shipping, Logistics, and Fleet Management**

The Shipping domain manages the physical bridge between the seller and the buyer. It handles the calculation of delivery
costs, carrier selection, and the management of an internal or third-party delivery fleet.1

### **High-Level Business Logic**

This module focuses on the operational efficiency of the fulfillment process. It must support multiple shipping
configurations (Flat Rate, Zone-based, or Carrier-based) and provide real-time tracking for the customer.4

| Component              | Logic Requirement                                                                    |
|:-----------------------|:-------------------------------------------------------------------------------------|
| **Shipping Zones**     | Defining geographical areas and their associated delivery costs.6                    |
| **Delivery Boy Fleet** | Management of internal personnel, including earnings, assignments, and performance.4 |
| **3PL Integration**    | Coordination with external couriers like Steadfast, Pathao, or Shiprocket.1          |
| **Pickup Points**      | Managing physical locations for customer self-collection.2                           |

### **Low-Level Technical Mapping**

The shipping domain suffers from excessive reliance on conditional blocks within the CartController to determine the
delivery cost for different product types.4

| Component                 | Technical Detail                                                                                                                    |
|:--------------------------|:------------------------------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\ShippingController, App\\Http\\Controllers\\DeliveryBoyController.4                                         |
| **Tables Involved**       | shipping\_zones, carriers, delivery\_boys, pickup\_points, cities, countries.4                                                      |
| **Primary Relationships** | Order hasOne DeliveryBoy; ShippingZone hasMany Cities; Product belongsTo ShippingConfiguration.4                                    |
| **Critical Files**        | app/Models/DeliveryBoy.php, app/Http/Controllers/Api/DeliveryBoyController.php, app/Services/ShippingService.php (Needs Creation).4 |

Refactoring requires the implementation of a LogisticsOrchestrator that can delegate cost calculation to the correct "
Strategy" based on admin settings (e.g., CategoryWiseShippingStrategy).19

### **Execution Plan**

| Step                     | Task Description                                                                                                      |
|:-------------------------|:----------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Audit how different shipping "Add-ons" (Shiprocket, Pathao) hook into the core order status lifecycle.1               |
| **1\. Data Integrity**   | Ensure that shipping weight and dimension fields use consistent units (e.g., kg/cm) to support carrier APIs.9         |
| **2\. Logic Extraction** | Move the "Delivery Boy" assignment and commission calculation into a dedicated LogisticsService.8                     |
| **3\. API Hardening**    | Secure the real-time tracking endpoints to ensure location data is only visible to the relevant customer and admin.21 |

### **Hidden Risks and Security Surface**

Refactoring shipping logic can break the "Checkout" flow if shipping rates cannot be calculated dynamically. Security
risks include "Address Injection" and potential privacy leaks regarding delivery personnel's real-time GPS locations if
the API is not properly scoped.21

## **Support Ticketing and Communication Infrastructure**

The Support domain manages the marketplace's relationship management layer. It facilitates communication between buyers
and sellers, as well as providing a structured channel for administrative assistance.2

### **High-Level Business Logic**

This module ensures marketplace health by resolving disputes and providing product clarity. It maintains an auditable
trail of all communications to protect the platform against fraud and facilitate dispute resolution.3

| Communication Channel | Business Logic Overview                                                        |
|:----------------------|:-------------------------------------------------------------------------------|
| **Support Tickets**   | Formal requests to the Admin for technical or billing issues.4                 |
| **Buyer-Seller Chat** | Informal pre-purchase inquiries and order-status discussions.2                 |
| **Product Inquiries** | Public or private questions on product detail pages regarding specifications.1 |
| **Notifications**     | Multichannel alerts (Email, SMS, Push) for key lifecycle events.4              |

### **Low-Level Technical Mapping**

Communication logic is currently scattered across SupportTicketController and ConversationController, with many
redundant functions for handling file attachments and message rendering.4

| Component                 | Technical Detail                                                                                      |
|:--------------------------|:------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\SupportTicketController, App\\Http\\Controllers\\ConversationController.4     |
| **Tables Involved**       | tickets, ticket\_replies, conversations, messages, notifications.4                                    |
| **Primary Relationships** | Ticket belongsTo User; Conversation hasMany Messages; Message belongsTo User.4                        |
| **Likely Laravel Files**  | app/Events/MessageSent.php, app/Notifications/SupportTicketUpdated.php, app/Models/Conversation.php.4 |

The extraction boundary should focus on a CommunicationService that handles the common logic for file sanitization,
message persistence, and notification dispatching across all channels.19

### **Execution Plan**

| Step                     | Task Description                                                                                                          |
|:-------------------------|:--------------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Analyze the "Support Board" and "WhatsApp" add-ons to see how they integrate with internal message logs.22                |
| **1\. Data Integrity**   | Implement a "Soft Delete" and archival strategy for old messages to prevent the messages table from bloating performance. |
| **2\. Logic Extraction** | Move message-routing and notification-triggering logic into a NotificationService.19                                      |
| **3\. API Hardening**    | Implement strict file-type validation for ticket attachments and protect against XSS in chat messages.21                  |

### **Hidden Risks and Security Surface**

Refactoring this domain can cause "Silent Failures" in customer support if notification emails are not correctly routed.
Security risks include "Information Disclosure" through unsanitized message threads and the risk of "Remote Code
Execution" (RCE) via dangerous file uploads in support tickets.21

## **Point of Sale (POS) and Inventory Synchronization**

The POS module bridges the gap between the online marketplace and physical retail operations. It allows sellers to
process in-person sales using the same inventory and customer database as the online platform.6

### **High-Level Business Logic**

The POS system facilitates rapid, face-to-face transactions. It must handle barcode scanning, thermal receipt printing,
and immediate inventory deduction across all channels.7

| Business Requirement     | Logic Application                                                                                 |
|:-------------------------|:--------------------------------------------------------------------------------------------------|
| **Inventory Sync**       | Atomic deduction of stock to prevent overselling across online and offline channels.6             |
| **Staff Management**     | Permission-based access for cashiers and store managers.7                                         |
| **Offline Handling**     | (Ideally) ability to queue transactions if the connection to the central server is intermittent.6 |
| **Hardware Integration** | formatting data for thermal printers and barcode scanners.7                                       |

### **Low-Level Technical Mapping**

The POS module is currently implemented as a separate controller that duplicates much of the CheckoutController logic
for order creation and inventory management.7

| Component                 | Technical Detail                                                                                                     |
|:--------------------------|:---------------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\PosController.7                                                                              |
| **Tables Involved**       | pos\_configs, products, product\_stocks, orders, users.4                                                             |
| **Primary Relationships** | Order belongsTo User; Product hasMany ProductStocks.4                                                                |
| **Likely Laravel Files**  | app/Http/Controllers/Api/PosController.php, resources/views/backend/pos/index.blade.php, app/Models/PosConfig.php.18 |

Refactoring requires the extraction of a UnifiedInventoryService and UnifiedOrderService that can be shared by both the
Web/Mobile Checkout and the POS interface.19

### **Execution Plan**

| Step                     | Task Description                                                                                                    |
|:-------------------------|:--------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Review the "Barcode" and "POS Manager" add-ons to identify redundant inventory checks.7                             |
| **1\. Data Integrity**   | Ensure the POS order flow correctly updates the combined\_orders and order\_details tables with decimal precision.4 |
| **2\. Logic Extraction** | Implement a PosService to handle staff shifts and thermal printer formatting.19                                     |
| **3\. API Hardening**    | Restrict POS endpoints to authenticated staff and implement IP-whitelisting for known physical store locations.21   |

### **Hidden Risks and Security Surface**

Refactoring POS logic can lead to "Inventory Mismatches" if the stock-locking mechanism is not consistent with the
online checkout flow. Security risks include "Staff Fraud" through manual price overrides and unauthorized discount
applications.7

## **Wallet, Ledger, and Financial Auditing**

The Wallet domain acts as the marketplace's internal financial clearinghouse. It tracks customer balances, seller
earnings, and administrative commissions through a ledger-based system.4

### **High-Level Business Logic**

The wallet module provides a layer of abstraction between orders and actual bank transfers. It allows for immediate "
Store Credit" and facilitates complex payout structures in a multi-vendor environment.4

| Ledger Entry Type         | Business Purpose                                                                          |
|:--------------------------|:------------------------------------------------------------------------------------------|
| **Customer Top-up**       | converting external currency into platform-specific credits.4                             |
| **Seller Earnings**       | credit applied to the seller after successful order completion and commission deduction.4 |
| **Refunds**               | immediate restoration of funds to the customer wallet for cancelled orders.4              |
| **Affiliate Commissions** | reward distribution for referrals and marketing partners.4                                |

### **Low-Level Technical Mapping**

The current wallet system is highly procedural, with balance updates often happening directly in controllers through
simple increment/decrement operations without proper ledger records.4

| Component                 | Technical Detail                                                                                                        |
|:--------------------------|:------------------------------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\WalletController, App\\Http\\Controllers\\SellerController.4                                    |
| **Tables Involved**       | wallets, wallet\_transactions, seller\_payouts, affiliate\_logs.4                                                       |
| **Primary Relationships** | Wallet belongsTo User; WalletTransaction belongsTo Wallet; Order relates to WalletTransaction.4                         |
| **Critical Files**        | app/Models/Wallet.php, app/Services/LedgerService.php (Needs Creation), app/Http/Controllers/Api/WalletController.php.4 |

Extraction boundaries must ensure that a WalletService handles all financial movements, wrapping them in database
transactions to prevent partial updates.19

### **Execution Plan**

| Step                     | Task Description                                                                                                      |
|:-------------------------|:----------------------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Identify all points in the application where a user or seller balance is modified (e.g., Refund, Order, Top-up).4     |
| **1\. Data Integrity**   | Audit the wallet\_transactions table to ensure every credit has a corresponding order or payment ID as a reference.20 |
| **2\. Logic Extraction** | Move balance update logic to a FinancialService that strictly enforces "Double-Entry" bookkeeping principles.19       |
| **3\. API Hardening**    | Protect against "Negative Balance" attacks and ensure transaction history visibility is strictly user-scoped.21       |

### **Hidden Risks and Security Surface**

Refactoring this domain is high-risk; any error can lead to "Phantom Credits" or "Lost Earnings." Security risks
include "Balance Injection" and potential "Integer Overflow" if amounts are not handled with arbitrary-precision math (
BCMath).20

## **System Configuration and Globalization Module**

The Configuration domain manages the behavioral flags and localization settings that drive the marketplace's dynamic
personality. It is the control room from which the Admin modulates the entire platform.3

### **High-Level Business Logic**

This module manages the metadata that determines how other modules behave. It facilitates the platform's ability to
operate in different markets by managing multiple currencies, languages, and regional business rules.2

| Configuration Type  | Business Role                                                         |
|:--------------------|:----------------------------------------------------------------------|
| **Feature Toggles** | enabling or disabling multi-vendor, classifieds, or auction systems.3 |
| **Localization**    | Multi-language translation management and RTL support.2               |
| **Currency Logic**  | Exchange rate management and base-currency normalization.2            |
| **Infrastructure**  | SMTP settings, S3 storage configurations, and social API keys.8       |

### **Low-Level Technical Mapping**

The system relies on a massive business\_settings table that stores key-value pairs, which are often retrieved in an
unoptimized way, leading to high database overhead.8

| Component                 | Technical Detail                                                                                 |
|:--------------------------|:-------------------------------------------------------------------------------------------------|
| **God Classes**           | App\\Http\\Controllers\\BusinessSettingsController, App\\Http\\Controllers\\LanguageController.8 |
| **Tables Involved**       | business\_settings, languages, translations, currencies.4                                        |
| **Primary Relationships** | Translation relates polymorphic to various entities (Product, Category, etc.).4                  |
| **Critical Files**        | app/Helpers/CoreComponent.php, config/app.php, resources/lang, app/Models/BusinessSetting.php.4  |

The extraction boundary should involve a ConfigurationRepository that utilizes Laravel's Cache to serve settings with
minimal latency.4

### **Execution Plan**

| Step                     | Task Description                                                                                          |
|:-------------------------|:----------------------------------------------------------------------------------------------------------|
| **0\. Discovery**        | Audit all calls to get\_setting() to identify frequently accessed configurations for caching.4            |
| **1\. Data Integrity**   | Ensure that currencies exchange rates are stored with high precision to avoid conversion losses.20        |
| **2\. Logic Extraction** | Move currency conversion and translation lookup into dedicated LocalizationService and SettingsService.19 |
| **3\. API Hardening**    | Mask sensitive keys in the Admin UI and ensure translation-upload endpoints validate file integrity.8     |

### **Hidden Risks and Security Surface**

Refactoring this domain can cause "Platform Blackouts" if the base configuration is corrupted. Security risks include
the "Exposure of Secrets" through unmasked admin forms and potential "SQL Injection" in translation lookup queries.8

## **Domain Prioritization and Impact Assessment**

To successfully refactor the Active eCommerce CMS, a prioritized roadmap is essential. The strategy focuses on
establishing the core "Foundational Services" before addressing the high-stakes "Transactional Modules."

| Priority | Module                 | Assessment of Complexity & Risk                                                      |
|:---------|:-----------------------|:-------------------------------------------------------------------------------------|
| **1**    | Auth & User Management | High impact foundation; low risk if using Laravel's native auth features.4           |
| **2**    | System Config          | Medium complexity; foundational for feature toggles and localized pricing.8          |
| **3**    | Product Catalog        | High complexity; core data model for all other features.4                            |
| **4**    | Pricing & Coupons      | Medium complexity; essential for correct checkout logic.1                            |
| **5**    | Cart & Checkout        | High complexity; critical customer-facing path.4                                     |
| **6**    | Orders & Fulfillment   | High risk; requires atomic database transactions and state management.4              |
| **7**    | Payment Systems        | Highest risk; requires rigorous integration testing and external API coordination.20 |
| **8**    | Wallet & Ledger        | High risk; requires financial auditing and ledger-based tracking.7                   |
| **9**    | Shipping & Logistics   | Medium complexity; depends on multiple third-party courier add-ons.1                 |
| **10**   | Multi-Vendor           | High complexity; governs the relationship between Admin and Seller.7                 |
| **11**   | Support & Messaging    | Low risk; independent communication layer.3                                          |
| **12**   | POS Integration        | Medium complexity; final layer for omni-channel synchronization.18                   |

## **Conclusion and Strategic Refactoring Summary**

The refactoring of the Active eCommerce CMS necessitates a departure from the procedural "Script" mentality toward a
professional "Platform" architecture. By decomposing the monolith into these twelve distinct domains, the system gains
the modularity required to sustain its position as a leading marketplace solution. The transition from floating-point
double types to arbitrary-precision decimal ensures financial auditability, while the extraction of logic from
controllers into Service classes adheres to the SOLID principles of clean code. This architecture not only enhances
security against common vulnerabilities like SQLi and IDOR but also provides the isolation necessary to scale individual
modules independently, such as moving the Product Catalog to Elasticsearch or the Messaging domain to a dedicated
WebSocket server. Ultimately, this roadmap transforms the technical debt of a legacy monolith into the strategic asset
of a modern, domain-driven eCommerce ecosystem.5

#### **Works cited**

1. Active eCommerce CMS by ActiveITzone \- CodeCanyon, accessed January 26,
   2026, [https://codecanyon.net/item/active-ecommerce-cms/23471405](https://codecanyon.net/item/active-ecommerce-cms/23471405)
2. Buy Active eCommerce CMS: Just $15 \- Theme Canal, accessed January 26,
   2026, [https://www.themecanal.com/product/active-ecommerce-cms/](https://www.themecanal.com/product/active-ecommerce-cms/)
3. Active eCommerce CMS \- Active IT Zone Limited, accessed January 26,
   2026, [https://activeitzone.com/active-ecommerce-cms](https://activeitzone.com/active-ecommerce-cms)
4. Active eCommerce CMS \- Flutter App Marketplace, accessed January 26,
   2026, [https://market.flutterappworld.com/product/active-ecommerce-cms/](https://market.flutterappworld.com/product/active-ecommerce-cms/)
5. 10 best eCommerce PHP Scripts 2026 \- Xgenious, accessed January 26,
   2026, [https://xgenious.com/ecommerce-php-script/](https://xgenious.com/ecommerce-php-script/)
6. Best Alternative Of Active eCommerce CMS In Codecanyon \- 6amTech, accessed January 26,
   2026, [https://6amtech.com/blog/best-alternative-of-active-ecommerce-cms/](https://6amtech.com/blog/best-alternative-of-active-ecommerce-cms/)
7. Free Demo Active eCommerce CMS in Dehradun | ID: 2851964894462 \- IndiaMART, accessed January 26,
   2026, [https://www.indiamart.com/proddetail/free-demo-active-ecommerce-cms-2851964894462.html](https://www.indiamart.com/proddetail/free-demo-active-ecommerce-cms-2851964894462.html)
8. The Shop PWA eCommerce CMS Guide | PDF | Php | Search Engine Optimization \- Scribd, accessed January 26,
   2026, [https://www.scribd.com/document/690932983/The-Shop-Documentation](https://www.scribd.com/document/690932983/The-Shop-Documentation)
9. Active Ecommerce CMS Documentation | PDF | Php | Login \- Scribd, accessed January 26,
   2026, [https://www.scribd.com/document/747976594/Active-Ecommerce-CMS-Documentation](https://www.scribd.com/document/747976594/Active-Ecommerce-CMS-Documentation)
10. I'm wanting to turn my website into a store for others. What's the best script I can use?, accessed January 26,
    2026, [https://www.quora.com/Im-wanting-to-turn-my-website-into-a-store-for-others-Whats-the-best-script-I-can-use](https://www.quora.com/Im-wanting-to-turn-my-website-into-a-store-for-others-Whats-the-best-script-I-can-use)
11. Active eCommerce CMS vs 6Valley: Which One Better?, accessed January 26,
    2026, [https://6valley.app/blog/active-ecommerce-cms-vs-6valley/](https://6valley.app/blog/active-ecommerce-cms-vs-6valley/)
12. Active eCommerce Flutter App by ActiveITzone \- CodeCanyon, accessed January 26,
    2026, [https://codecanyon.net/item/active-ecommerce-flutter-app/31466365](https://codecanyon.net/item/active-ecommerce-flutter-app/31466365)
13. ActiveITzone \- Portfolio \- CodeCanyon, accessed January 26,
    2026, [https://codecanyon.net/user/activeitzone/portfolio](https://codecanyon.net/user/activeitzone/portfolio)
14. billiemead/activeecommerce-form-builder: Active Ecommerce CMS Add-on for Creating a Drag-and-Drop Form Builder Using
    JQuery Form Builder \- GitHub, accessed January 26,
    2026, [https://github.com/billiemead/activeecommerce-form-builder](https://github.com/billiemead/activeecommerce-form-builder)
15. Laracasts Forum \-, accessed January 26,
    2026, [https://laracasts.com/discuss?q=test\&page=46](https://laracasts.com/discuss?q=test&page=46)
16. Support for Active eCommerce CMS \- CodeCanyon, accessed January 26,
    2026, [https://codecanyon.net/item/active-ecommerce-cms/23471405/support](https://codecanyon.net/item/active-ecommerce-cms/23471405/support)
17. Active ECommerce CMS Documentation \_ (v-10.0.0) | PDF | Php ..., accessed January 26,
    2026, [https://www.scribd.com/document/975380982/Active-ECommerce-CMS-Documentation-v-10-0-0](https://www.scribd.com/document/975380982/Active-ECommerce-CMS-Documentation-v-10-0-0)
18. Inventual – Complete POS, Inventory Website and Mobile Flutter App, accessed January 26,
    2026, [https://market.flutterappworld.com/product/inventual-complete-pos-inventory-website-and-mobile-flutter-app/](https://market.flutterappworld.com/product/inventual-complete-pos-inventory-website-and-mobile-flutter-app/)
19. Laravel Jobs for January 2026 \- Freelancer, accessed January 26,
    2026, [https://www.freelancer.com/jobs/laravel](https://www.freelancer.com/jobs/laravel)
20. Laravel Ziraatpay Gateway Module | Freelancer, accessed January 26,
    2026, [https://www.freelancer.com/projects/api-developmet/laravel-ziraatpay-gateway-module](https://www.freelancer.com/projects/api-developmet/laravel-ziraatpay-gateway-module)
21. Vulnerability Summary for the Week of July 3, 2023 | CISA, accessed January 26,
    2026, [https://www.cisa.gov/news-events/bulletins/sb23-191](https://www.cisa.gov/news-events/bulletins/sb23-191)
22. Active eCommerce CMS Plugins, Code & Scripts | CodeCanyon, accessed January 26,
    2026, [https://codecanyon.net/search/active%20ecommerce%20cms](https://codecanyon.net/search/active%20ecommerce%20cms)
23. Demo of Active eCommerce CMS, accessed January 26,
    2026, [https://demo.activeitzone.com/ecommerce-megamart/](https://demo.activeitzone.com/ecommerce-megamart/)
24. Active Ecommerce CMS Documentation | PDF | Php \- Scribd, accessed January 26,
    2026, [https://www.scribd.com/document/499270874/Active-Ecommerce-CMS-Documentation](https://www.scribd.com/document/499270874/Active-Ecommerce-CMS-Documentation)
25. Staging \- Shopware Documentation, accessed January 26,
    2026, [https://developer.shopware.com/docs/guides/hosting/configurations/shopware/staging.html](https://developer.shopware.com/docs/guides/hosting/configurations/shopware/staging.html)
26. TechnoTronixs \- image, accessed January 26,
    2026, [https://5.imimg.com/data5/SELLER/Doc/2023/9/340964544/NB/NT/CD/105750176/social-media-designing.pdf](https://5.imimg.com/data5/SELLER/Doc/2023/9/340964544/NB/NT/CD/105750176/social-media-designing.pdf)
27. 16 Best eCommerce CMS Platforms to Build Your Online Store \- 6Valley, accessed January 26,
    2026, [https://6valley.app/blog/best-ecommerce-cms-platforms/](https://6valley.app/blog/best-ecommerce-cms-platforms/)
28. Laravel Jobs, Employment \- Freelancer, accessed January 26,
    2026, [https://www.freelancer.com/job-search/laravel/](https://www.freelancer.com/job-search/laravel/)
29. Active eCommerce Seller Subscription Add-on at $3.49 only \- WPSHOP, accessed January 26,
    2026, [https://wpshop.net/shop/active-ecommerce-seller-subscription-add-on/](https://wpshop.net/shop/active-ecommerce-seller-subscription-add-on/)
30. Laravel eCommerce CMS Guide | PDF | Php | Websites \- Scribd, accessed January 26,
    2026, [https://www.scribd.com/document/753359294/Active-Ecommerce-CMS-Documentation](https://www.scribd.com/document/753359294/Active-Ecommerce-CMS-Documentation)
31. 14 Great Admin Panel Themes For E-Commerce | by Flatlogic Platform \- Medium, accessed January 26,
    2026, [https://medium.com/flatlogic/14-great-admin-panel-themes-for-e-commerce-6408ef5e8816](https://medium.com/flatlogic/14-great-admin-panel-themes-for-e-commerce-6408ef5e8816)
32. Changelog | MailBeez Ecommerce Email Marketing, accessed January 26,
    2026, [https://www.mailbeez.com/documentation/changelog](https://www.mailbeez.com/documentation/changelog)
33. ECommerce CMS Plugins, Code & Scripts | CodeCanyon, accessed January 26,
    2026, [https://codecanyon.net/search/ecommerce%20cms](https://codecanyon.net/search/ecommerce%20cms)
34. Script Installation | PDF | Php \- Scribd, accessed January 26,
    2026, [https://www.scribd.com/document/867903723/script-installation](https://www.scribd.com/document/867903723/script-installation)