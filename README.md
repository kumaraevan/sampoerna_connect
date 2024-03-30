# ecommerce

To run this website, use XAMPP Server. Host the necessary files in your htdocs.
To ensure that this website is running, you also need to set up the database and name it as ecommerce_db.

Create a database and create the tables.

CREATE TABLE users ( 
UserID INT AUTO_INCREMENT PRIMARY KEY, 
Name VARCHAR(255) NOT NULL, 
Email VARCHAR(255) NOT NULL, 
Phone CHAR(15), 
Password VARCHAR(255) NOT NULL, 
Address TEXT, 
Role ENUM('seller', 'buyer', 'admin') NOT NULL, 
RegistrationDate DATETIME NOT NULL 
);

CREATE TABLE categories ( 
CategoryID INT AUTO_INCREMENT PRIMARY KEY, 
CategoryName VARCHAR(255) NOT NULL);

CREATE TABLE products ( 
ProductID INT AUTO_INCREMENT PRIMARY KEY, 
SellerID INT, 
CategoryID INT, 
Name VARCHAR(255) NOT NULL, 
Description TEXT, 
Price DECIMAL(10,2) NOT NULL, 
StockQuantity INT NOT NULL, 
ImageURLs TEXT, 
DateAdded DATETIME NOT NULL, 
FOREIGN KEY (SellerID) REFERENCES users(UserID), 
FOREIGN KEY (CategoryID) REFERENCES categories(CategoryID) 
);

CREATE TABLE orders ( 
OrderID INT AUTO_INCREMENT PRIMARY KEY, 
BuyerID INT, 
TotalPrice DECIMAL(10,2) NOT NULL, 
OrderStatus ENUM('pending', 'completed', 'shipped', 'cancelled') NOT NULL, 
PaymentMethod VARCHAR(255), 
ShippingAddress TEXT, 
DateOrdered DATETIME NOT NULL, 
FOREIGN KEY (BuyerID) REFERENCES users(UserID) 
);

CREATE TABLE orderdetails ( 
OrderDetailID INT AUTO_INCREMENT PRIMARY KEY, 
OrderID INT, 
ProductID INT, 
Quantity INT NOT NULL, 
PriceAtPurchase DECIMAL(10,2) NOT NULL, 
FOREIGN KEY (OrderID) REFERENCES orders(OrderID), 
FOREIGN KEY (ProductID) REFERENCES products(ProductID) 
);

CREATE TABLE reviews ( 
ReviewID INT AUTO_INCREMENT PRIMARY KEY, 
ProductID INT, 
BuyerID INT, 
Rating INT NOT NULL, 
Comment TEXT, 
DatePosted DATETIME NOT NULL, 
FOREIGN KEY (ProductID) REFERENCES products(ProductID), 
FOREIGN KEY (BuyerID) REFERENCES users(UserID) 
);

CREATE TABLE payment ( 
PaymentID INT AUTO_INCREMENT PRIMARY KEY, 
OrderID INT, 
Date DATE NOT NULL, 
FOREIGN KEY (OrderID) REFERENCES orders(OrderID) 
);

CREATE TABLE deliveries ( 
DeliveryID INT AUTO_INCREMENT PRIMARY KEY, 
OrderID INT, 
Date DATE NOT NULL, 
FOREIGN KEY (OrderID) REFERENCES orders(OrderID) 
);

CREATE TABLE transaction_reports ( 
ReportID INT AUTO_INCREMENT PRIMARY KEY, 
CustomerID INT, 
OrderID INT, 
ProductID INT, 
PaymentID INT, 
FOREIGN KEY (CustomerID) REFERENCES users(UserID), 
FOREIGN KEY (OrderID) REFERENCES orders(OrderID), 
FOREIGN KEY (ProductID) REFERENCES products(ProductID), 
FOREIGN KEY (PaymentID) REFERENCES payment(PaymentID) 
);

CategoryName inside category table
INSERT INTO categories (CategoryName) VALUES ('Electronics');
INSERT INTO categories (CategoryName) VALUES ('Fashion');
INSERT INTO categories (CategoryName) VALUES ('Home and Kitchen');
INSERT INTO categories (CategoryName) VALUES ('Beauty and Personal Care');
INSERT INTO categories (CategoryName) VALUES ('Sports and Outdoors');
INSERT INTO categories (CategoryName) VALUES ('Toys and Games');
INSERT INTO categories (CategoryName) VALUES ('Books and Stationery');
INSERT INTO categories (CategoryName) VALUES ('Health and Wellness');
INSERT INTO categories (CategoryName) VALUES ('Groceries and Gourmet Foods');
INSERT INTO categories (CategoryName) VALUES ('Automotive');
