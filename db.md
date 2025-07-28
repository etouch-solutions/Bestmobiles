
CREATE TABLE Branch_Master (
    Branch_Id INT AUTO_INCREMENT PRIMARY KEY,
    Branch_Name VARCHAR(50),
    Branch_Head_Name VARCHAR(50),
    Branch_Address VARCHAR(250),
    Branch_CNo BIGINT,
    Branch_Status BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE Customer_Master (
    Cus_Id INT AUTO_INCREMENT PRIMARY KEY,
    Cus_Name VARCHAR(50),
    Cus_CNo BIGINT,
    Cus_Address VARCHAR(250),
    Cus_Email VARCHAR(50),
    Cus_Ref_Name VARCHAR(50),
    Cus_Ref_CNo BIGINT,
    Branch_Id INT,
    Cus_Photo_Path VARCHAR(255),
    Cus_Id_Copy_Path VARCHAR(255),
    Is_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Branch_Id) REFERENCES Branch_Master(Branch_Id)
);


CREATE TABLE Staff_Master (
    Staff_Id INT AUTO_INCREMENT PRIMARY KEY,
    Staff_Name VARCHAR(50),
    Staff_CNo BIGINT,
    Staff_Email VARCHAR(50),
    Staff_Address VARCHAR(250),
    Staff_Designation VARCHAR(50),
    Is_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE Brands_Master (
    Brand_Id INT AUTO_INCREMENT PRIMARY KEY,
    Brand_Name VARCHAR(50),
    Is_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE Insurance_Master (
    Insurance_Id INT AUTO_INCREMENT PRIMARY KEY,
    Insurance_Name VARCHAR(50),
    Insurance_Description VARCHAR(250),
    Premium_Percentage INT,
    Duration VARCHAR(50),
    Is_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE Insurance_Entry (
    Insurance_Entry_Id INT AUTO_INCREMENT PRIMARY KEY,
    Cus_Id INT,
    Brand_Id INT,
    Insurance_Id INT,
    Staff_Id INT,
    Product_Model_Name VARCHAR(50),
    IMEI_1 VARCHAR(50) UNIQUE,
    IMEI_2 VARCHAR(50) UNIQUE,
    Product_Value INT,
    Bill_Copy_Path VARCHAR(255),
    Product_Photo_Path VARCHAR(255),
    Bill_Date DATE,
    Insurance_Start_Date DATE,
    Insurance_End_Date DATE,
    Premium_Amount INT,
    Is_Product_Covered BOOLEAN,
    Is_Insurance_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Cus_Id) REFERENCES Customer_Master(Cus_Id),
    FOREIGN KEY (Brand_Id) REFERENCES Brands_Master(Brand_Id),
    FOREIGN KEY (Insurance_Id) REFERENCES Insurance_Master(Insurance_Id),
    FOREIGN KEY (Staff_Id) REFERENCES Staff_Master(Staff_Id)
);


CREATE TABLE Defect_Master (
    Defect_Id INT AUTO_INCREMENT PRIMARY KEY,
    Defect_Name VARCHAR(50),
    Defect_Description VARCHAR(250),
    Is_Active BOOLEAN,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP
);


## CREATE TABLE Claim_Entry (
    Claim_Id INT AUTO_INCREMENT PRIMARY KEY,
    Insurance_Entry_Id INT,
    Staff_Id INT,
    Claim_Date DATE,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    Updated_At DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Insurance_Entry_Id) REFERENCES Insurance_Entry(Insurance_Entry_Id),
    FOREIGN KEY (Staff_Id) REFERENCES Staff_Master(Staff_Id)
);

CREATE TABLE Claim_Defects (
    Claim_Id INT,
    Defect_Id INT,
    Defect_Value INT,
    Defect_Description VARCHAR(250),
    PRIMARY KEY (Claim_Id, Defect_Id),
    FOREIGN KEY (Claim_Id) REFERENCES Claim_Entry(Claim_Id),
    FOREIGN KEY (Defect_Id) REFERENCES Defect_Master(Defect_Id)
);



| Left columns  | Right columns |
| ------------- |:-------------:|
| left foo      | right foo     |
| left bar      | right bar     |
| left baz      | right baz     |

-- -------------------------------------------------------------- -------------------------------------------------------------- ------------------------------------------------------------
-- Enforce uniqueness on Branch_Master
-- -----------------------------------------------------------
-- WHY:
-- To ensure that no two branches have the exact same name 
-- and address combination, which could lead to duplication 
-- and data inconsistency.
--
-- TABLE: Branch_Master
-- COLUMNS: Branch_Name, Branch_Address
--
-- This ALTER TABLE statement adds a unique constraint that
-- prevents inserting or updating rows with duplicate 
-- (Branch_Name, Branch_Address) pairs.
-- ------------------------------------------------------------

ALTER TABLE Branch_Master
ADD CONSTRAINT unique_branch UNIQUE (Branch_Name, Branch_Address);
-- -------------------------------------------------------------- -------------------------------------------------------------- ------------------------------------------------------------