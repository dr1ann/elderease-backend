create table listahanAffil
(
    oscaID INT Not Null,
    listahanDesc VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table indiAffil
(
    oscaID INT Not Null,
    indiDesc VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table otherAffil
(
    oscaID INT Not Null,
    otherDesc VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table bin_dswd
(
    oscaID INT Not Null,
    seniorAffilNo INT Not Null,
    lastName VARCHAR(250),
    firstName VARCHAR(250),
    middleName VARCHAR(250),
    suffix VARCHAR(250),
    age INT(5),
    gender VARCHAR(250),
    civilStat VARCHAR(250),
    religion VARCHAR(250),
    birthday date,
    placeOfBirth VARCHAR(250),
    educAttain VARCHAR(250),
    famName VARCHAR(250),
    famRelation VARCHAR(250),
    famAge INT(5),
    famCivilStat VARCHAR(250),
    famOccu VARCHAR(250),
    address VARCHAR(250),
    livingArr VARCHAR(250),
    tin INT(50),
    philHealth INT(50),
    dswdPensioner VARCHAR(250),
    regSupport VARCHAR(250),
    psource VARCHAR(250),
    psource_desc VARCHAR(250),
    famIncome INT(50),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID),
    FOREIGN KEY (seniorAffilNo) REFERENCES seniorAffil(affilNo)
);

create table bin_scc
(
    oscaID INT Not Null,
    reqID INT Not Null,
    receiptNo INT Not Null,
    lastName VARCHAR(250),
    firstName VARCHAR(250),
    middleName VARCHAR(250),
    gender VARCHAR(250),
    age INT(5),
    birthday date,
    placeOfBirth VARCHAR(250),
    civilStat VARCHAR(250),
    address VARCHAR(250),
    contactNum INT(20),
    payDate date,
    amount INT(50),
    payDesc VARCHAR(250),
    modePay VARCHAR(250),
    contrNum INT(50),
    authorAgent VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID),
    FOREIGN KEY (reqID) REFERENCES requirements(reqID),
    FOREIGN KEY (receiptNo) REFERENCES payments(receiptNo)
);

create table officers
(
    oscaID INT Not Null,
    name VARCHAR(250),
    position VARCHAR(250),
    year VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table payments
(
    receiptNo INT NOT NULL PRIMARY KEY,
    oscaID INT Not Null,
    payDate date,
    amount INT(50),
    payDesc VARCHAR(250),
    modePay VARCHAR(250),
    balance INT(50),
    authorAgent VARCHAR(250),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table seniorReq
(
    oscaID INT Not Null,
    reqID INT,
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID),
    FOREIGN KEY (reqID) REFERENCES requirements(reqID)
);

create table requirements
(
    reqID INT PRIMARY KEY,
    req VARCHAR(250)
);

create table seniorAffil
(
    oscaID INT Not Null,
    affilNo INT,
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID),
    FOREIGN KEY (affilNo) REFERENCES affilation(affilNo)
);

create table affilation
(
    affilNo INT PRIMARY KEY,
    affilDesc VARCHAR(250)
);

create table famcom
(
    oscaID INT NOT NULL,
    name VARCHAR(250),
    relation VARCHAR(250),
    age INT(5),
    civilStat VARCHAR(250),
    occupation VARCHAR(250),
    income INT(50),
    FOREIGN KEY (oscaID) REFERENCES scInfo(oscaID)
);

create table scInfo
(
    oscaID Int Not Null PRIMARY KEY,
    lastName VARCHAR(250),
    firstName VARCHAR(250),
    middleName VARCHAR(250),
    suffix VARCHAR(250),
    gender VARCHAR(250),
    birthday Date,
    age INT(5),
    placeOfBirth VARCHAR(250),
    civilStat VARCHAR(250),
    contactNum INT(20),
    address VARCHAR(250),
    religion VARCHAR(250),
    citizenship VARCHAR(250),
    educAttain VARCHAR(250),
    tin INT(50),
    philHealth INT(50),
    dswdPensioner VARCHAR(250),
    livingArr VARCHAR(250),
    psource VARCHAR(250),
    psource_desc VARCHAR(250),
    contrNum INT(50),
    regSupport VARCHAR(250),
    archive VARCHAR(50)
);