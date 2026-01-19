SELECT * FROM CARTITEM;
SELECT * FROM CLOTH;
SELECT * FROM CUSTOMER;
SELECT * FROM ITEM;
SELECT * FROM ITEMCATEGORY;
SELECT * FROM PURCHASEORDER;
SELECT * FROM PURCHASEORDERDETAIL;
SELECT * FROM SALE;
SELECT * FROM SALEDETAIL;
SELECT * FROM STAFF;
SELECT * FROM USERS;
SELECT * FROM VENDOR;

DESC CARTITEM;
DESC CLOTH;
DESC CUSTOMER;
DESC ITEM;
DESC ITEMCATEGORY;
DESC PURCHASEORDER;
DESC PURCHASEORDERDETAIL;
DESC SALE;
DESC SALEDETAIL;
DESC STAFF;
DESC USERS;
DESC VENDOR;

COMMIT;

INSERT INTO STAFF VALUES (241, 'Clerk', 3500, SYSDATE, 221);
INSERT INTO STAFF VALUES (261, 'System Designer', 8000, SYSDATE, 221);

SELECT  
                    STAFF.ID AS ID,
                    STAFF.NAME AS STAFFNAME,
                    STAFF.EMAIL AS EMAIL,
                    STAFF.PHONENUMBER AS PHONENUMBER,
                    Position,
                    Salary,
                    DateHired,
                    MGR.NAME AS MANAGEDBYNAME,
                    STAFF.ROLE AS ROLE
                FROM staff 
                LEFT JOIN USERS STAFF ON staff.StaffID = STAFF.ID 
                LEFT JOIN USERS MGR ON staff.MANAGEDBY = MGR.ID
                WHERE LOWER(STAFF.ROLE) = 'staff' OR LOWER(STAFF.ROLE) = 'admin'
                ORDER BY STAFFNAME ASC;
                
delete from users
where id = 301;

SELECT 
                    SALEID, 
                    TO_CHAR(SALEDATETIME,'DD-MM-YY HH:MI:SS AM') AS SALEDATETIME,
                    TOTALPRICE,
                    CUSTOMERID,
                    STAFFID,
                    CUST.NAME AS CUSTOMER_NAME,
                    COALESCE(STAFF.NAME, 'N/A') AS STAFF_NAME 
                    FROM SALE 
                    LEFT JOIN USERS CUST 
                        ON SALE.CUSTOMERID = CUST.ID 
                    LEFT JOIN USERS STAFF 
                        ON SALE.STAFFID = STAFF.ID 
                    ORDER BY SALEDATETIME DESC