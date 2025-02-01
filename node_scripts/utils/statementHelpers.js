const exactMatchDate = /^((0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4})$/;

const TRANSACTIONS_TABLE_START = "DebitCreditDetalii tranzactieData";
const TRANSACTIONS_TABLE_END = "Sold initial:";

const BT_TRANSACTIONS_TABLE_START = (line) => {
    return (
        line == "DataDescriere  DebitCredit" ||
        line == "DataDescriere DebitCredit"
    );
};
const BT_TRANSACTIONS_DAY_END = (line, currentDate = null) => {
    if (currentDate && exactMatchDate.test(line)) {
        return true;
    }

    if (line.includes("RULAJ ZI")) {
        return true;
    }

    return false;
};

const TERMINAL_FLAG = "Terminal:";
const ORDONATOR_FLAG = "Ordonator:";
const LOCATION_FLAGS = [TERMINAL_FLAG, ORDONATOR_FLAG];

const CUMPARARE_POS = "Cumparare POS";
const INCASARE = "Incasare";
const TAXE_SI_COMISIOANE = "Taxe si comisioane";
const COMISION_OPERATIUNE = "Comision pe operatiune";
const TRANSFER_HOME_BANK = "Transfer Home'Bank";
const RETRAGERE_NUMERAR = "Retragere numerar";
const TRANSFER_TERT = "Transfer Tert Prestator Servicii Plata";
const RATA_CREDIT = "Rata Credit";
const ING_ASIGURARE_CREDIT = "Prima asigurare ING Credit Protect";

const BT_TRANSACTION_KEYWORDS = [
    "Plata la POS",
    "Plata la POS non-BT cu card VISA",
    "Retragere de numerar de la ATM BT",
    "Comision incasare OP",
    "Incasare OP",
    "Incasare Instant",
    "Rambursare principal credit",
    "Dobanda credit",
    "Abonament BT 24",
    "Depunere numerar ATM",
    "Plata OP intra - canal electronic",
    "365",
];

const {
    getTransactionTypeByLabel,
    isNumericValue,
    getNumericValue,
} = require("./transactionHelpers");

const BT_DEFAULT_DELIMITOR = "Clasificare BT: Uz Intern";
const ING_DEFAULT_DELIMITOR = "Roxana Petria";

const getStatementBank = (data) => {
    if (data.includes("RB-PJS-40 024/18.02.99")) {
        return "ING";
    }
    // if (data.includes("BANCA TRANSILVANIA S. A.")) {
    if (
        data.includes("J12 / 4155 / 1993 • R.B. - P.J.R - 12 - 019") ||
        data.includes("J12/4155/1993 • R.B. - P.J.R-12-019")
    ) {
        return "BT";
    }
    if (data.includes("REVOLT21")) {
        return "REV";
    }
    return null;
};

const extractCurrency = (text) => {
    // List of expected currencies
    const validCurrencies = ["RON", "EUR", "USD"];

    // Regular expression to match any of the valid currencies possibly followed by more text
    const currencyRegex = new RegExp(
        `(${validCurrencies.join("|")})([A-Z]*\\d*|[A-Z]+\\d*)`,
        "g"
    );
    let matches;
    let foundCurrencies = [];

    while ((matches = currencyRegex.exec(text)) !== null) {
        foundCurrencies.push(matches[1]);
    }

    // Return the first valid currency found or null if none
    return foundCurrencies.length > 0 ? foundCurrencies[0] : null;
};

function extractInitialBalance(text, bank) {
    switch (bank) {
        case "ING":
            const initialBalanceRegex =
                /Sold initial:\s*(\d{1,3}(?:\.\d{3})*,\d{2})/;
            const match1 = text.match(initialBalanceRegex);
            if (match1) {
                // Replace dots with empty strings and commas with dots for correct float conversion
                const initialBalance = match1[1]
                    .replace(/\./g, "")
                    .replace(/,/g, ".");
                return parseFloat(initialBalance);
            }
            return null; // Return null if no match is found
            break;
        case "BT":
            // Define a regular expression pattern to identify the line with "SOLD ANTERIOR" and extract the following amount
            const balanceRegex =
                /SOLD ANTERIOR\s*\n(\d{1,3}(?:,\d{3})*(?:\.\d{2})?)/;
            const match2 = text.match(balanceRegex);
            if (match2 && match2[1]) {
                // Replace commas used as thousand separators (if any) and convert the string to a float number
                const balance = parseFloat(match2[1].replace(/,/g, ""));
                return balance;
            }
            return null; // Return null if no match is found or if parsing fails
            break;

        default:
            return null;
            break;
    }
}

function extractFinalBalance(text, bank) {
    switch (bank) {
        case "ING":
            const finalBalanceRegex =
                /Sold final\s*(\d{1,3}(?:\.\d{3})*,\d{2})/;
            const match1 = text.match(finalBalanceRegex);
            if (match1) {
                // Replace dots with empty strings and commas with dots for correct float conversion
                const finalBalance = match1[1]
                    .replace(/\./g, "")
                    .replace(/,/g, ".");
                return parseFloat(finalBalance);
            }
            return null; // Return null if no match is found
            break;
        case "BT":
            // Define a regular expression pattern to identify the line with "SOLD FINAL CONT" and capture the following amount
            const balanceRegex =
                /SOLD FINAL CONT\s*\n(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?)/;
            const match2 = text.match(balanceRegex);
            if (match2 && match2[1]) {
                // Normalize the captured amount to handle different thousand separators and decimal points
                const normalizedAmount = match2[1]
                    .replace(/,/g, "")
                    .replace(/\./g, "");
                const balance = parseFloat(normalizedAmount) / 100; // Convert string to float and adjust for cents
                return balance;
            }
            return null; // Return null if no match is found
            break;

        default:
            return null;
            break;
    }
}

function extractIban(text) {
    // Enhanced regular expression to match an IBAN, including those with longer alphanumeric sequences
    const ibanRegex = /[A-Z]{2}\d{2}[A-Z0-9]{12,30}/;
    const match = text.match(ibanRegex);
    if (match) {
        return match[0]; // Return the matched IBAN
    }
    return null; // Return null if no IBAN is found
}

function extractStatementEarliestDate(text) {
    // Regular expression to match dates in format dd/mm/yyyy or dd-mm-yyyy
    const dateRegex = /(\d{2})[\/\-](\d{2})[\/\-](\d{4})/g;
    let earliestDate = null;
    let match;

    while ((match = dateRegex.exec(text)) !== null) {
        const dateStr = match[0]; // Capture the full date string
        const year = match[3];
        const month = match[2];
        const day = match[1];
        const currentDate = new Date(year, month - 1, day); // Create date object

        if (!earliestDate || currentDate < earliestDate) {
            earliestDate = currentDate; // Update if it's the earliest date found
        }
    }

    // Format the earliest date found as "dd-mm-yyyy" if any date was found
    if (earliestDate) {
        return earliestDate;
        // return `${String(earliestDate.getDate()).padStart(2, '0')}-${String(earliestDate.getMonth() + 1).padStart(2, '0')}-${earliestDate.getFullYear()}`;
    }
    return null; // Return null if no date is found
}

const getPriceFloatValue = (string) => {
    return parseFloat(string.replace(".", "").replace(",", "."));
};

const parseRomanianDate = (romanianDateString) => {
    // Map Romanian month names to numerical values
    const monthMap = {
        ianuarie: 0,
        februarie: 1,
        martie: 2,
        aprilie: 3,
        mai: 4,
        iunie: 5,
        iulie: 6,
        august: 7,
        septembrie: 8,
        octombrie: 9,
        noiembrie: 10,
        decembrie: 11,
    };

    // Split the Romanian date string into components
    const [day, monthName, year] = romanianDateString.split(" ");

    // Get the numerical month value from the monthMap
    const month = monthMap[monthName.toLowerCase()];

    // Return a Date object
    return new Date(Date.UTC(year, month, parseInt(day)));
};

const parseBtDate = (string) => {
    let elements = string.split("/");
    return new Date(
        Date.UTC(
            parseInt(elements[2]),
            parseInt(elements[1]) - 1,
            parseInt(elements[0])
        )
    );
};

const parseTransactions = (data, options = {}) => {
    switch (options.bank) {
        case "ING":
            return parseIngStatement(data, options);
        case "BT":
            return parseBtStatement(data, options);

        default:
            return false;
            break;
    }
};

const parseBtStatement = (data, options = {}) => {
    let transactions = [];
    let pages = [];
    let lines = [];
    let transactionsStart = false; // after a line like "01/08/2022" , also save the date, also check to not have already doen this date
    let insideTable = false; // after a line like "DataDescriere DebitCredit"

    let currentDay = null;
    let currentTranzactionLines = [];

    const pageDelimitor = options.pageDelimitor ?? BT_DEFAULT_DELIMITOR;
    pages = data.split(pageDelimitor);

    for (let i = 0; i < options.numpages; i++) {
        insideTable = false;
        pageLines = pages[i].split("\n").filter((x) => x.length > 1);

        for (let j = 0; j < pageLines.length; j++) {
            const line = pageLines[j].trim();

            if (BT_TRANSACTIONS_TABLE_START(line)) {
                insideTable = true;
            } else if (
                insideTable &&
                BT_TRANSACTIONS_DAY_END(line, currentDay)
            ) {
                currentDay = null;
                if (currentTranzactionLines.length) {
                    transactions.push(currentTranzactionLines);
                }
                currentTranzactionLines = [];
            } else if (
                insideTable &&
                !currentDay &&
                exactMatchDate.test(line)
            ) {
                currentDay = line;
            }

            // If is a transaction keyword,
            else if (
                insideTable &&
                currentDay &&
                BT_TRANSACTION_KEYWORDS.includes(line)
            ) {
                // close the current transaction
                transactions.push(currentTranzactionLines);

                // open a new trasaction
                currentTranzactionLines = [currentDay, line];
                // continue;
            }

            // if I already have an current open transaction, a current date, I am inside a table, => add to the current transaction
            else if (
                insideTable &&
                currentDay &&
                currentTranzactionLines.length > 0
            ) {
                currentTranzactionLines.push(line);
            }
        }
    }

    // Format tranzaction data from raw tranzaction item array
    return formatBtTransactionObjects(transactions, options);
};

const formatBtTransactionObjects = (rawTransaction, options = {}) => {
    let out = [];

    rawTransaction.forEach((rawItem) => {
        const item = {
            name: "",
            amount: "",
            date: "",
            details: [],
            location: "",
            type: "",
            currency: options.currency,
        };

        if (rawItem.length > 0) {
            for (let i = 0; i < rawItem.length; i++) {
                if (i === 0) {
                    item.date = parseBtDate(rawItem[i]);
                } else if (i === 1) {
                    item.name = rawItem[i];
                    item.type = getTransactionTypeByLabel(rawItem[i]);
                } else if (isNumericValue(rawItem[i])) {
                    item.amount = getNumericValue(rawItem[i]);
                } else if (
                    rawItem[i].includes(" TID ") ||
                    rawItem[i].includes(" TID:")
                ) {
                    item.location = rawItem[i];
                } else {
                    item.details.push(rawItem[i]);
                }
            }

            out.push(item);
        }
    });

    return out;
};

const parseIngStatement = (data, options = {}) => {
    let pages = [];
    let lines = [];

    const pageDelimitor = options.pageDelimitor ?? ING_DEFAULT_DELIMITOR;
    pages = data.split(pageDelimitor);

    for (let i = 0; i < options.numpages; i++) {
        pageLines = pages[i].split("\n").filter((x) => x.length > 1);
        let transactionsStart = 0;

        for (let j = 0; j < pageLines.length; j++) {
            if (
                transactionsStart === 0 &&
                pageLines[j] == TRANSACTIONS_TABLE_START
            ) {
                transactionsStart = 1;
            } else if (transactionsStart === 1) {
                if (
                    i == options.numpages - 1 &&
                    pageLines[j] == TRANSACTIONS_TABLE_END
                ) {
                    transactionsStart = 2;
                } else {
                    lines.push(pageLines[j]);
                }
            }
        }
    }

    return extractTransactionsFromLines(lines, options);
};

const isTransactionHeaderLine = (line) => {
    if (line.includes(CUMPARARE_POS)) {
        return true;
    }
    if (line.includes(INCASARE)) {
        return true;
    }
    if (line.includes(TAXE_SI_COMISIOANE)) {
        return true;
    }
    if (line.includes(COMISION_OPERATIUNE)) {
        return true;
    }
    if (line.includes(TRANSFER_HOME_BANK)) {
        return true;
    }
    if (line.includes(RETRAGERE_NUMERAR)) {
        return true;
    }
    if (line.includes(TRANSFER_TERT)) {
        return true;
    }
    if (line.includes(RATA_CREDIT)) {
        return true;
    }
    if (line.includes(ING_ASIGURARE_CREDIT)) {
        return true;
    }

    return false;
};
const getTransactionType = (line) => {
    if (line.includes(CUMPARARE_POS)) {
        return CUMPARARE_POS;
    }
    if (line.includes(INCASARE)) {
        return INCASARE;
    }
    if (line.includes(TAXE_SI_COMISIOANE)) {
        return TAXE_SI_COMISIOANE;
    }
    if (line.includes(COMISION_OPERATIUNE)) {
        return COMISION_OPERATIUNE;
    }
    if (line.includes(TRANSFER_HOME_BANK)) {
        return TRANSFER_HOME_BANK;
    }
    if (line.includes(RETRAGERE_NUMERAR)) {
        return RETRAGERE_NUMERAR;
    }
    if (line.includes(TRANSFER_TERT)) {
        return TRANSFER_TERT;
    }
    if (line.includes(RATA_CREDIT)) {
        return RATA_CREDIT;
    }
    if (line.includes(ING_ASIGURARE_CREDIT)) {
        return ING_ASIGURARE_CREDIT;
    }

    return null;
};

const formatIngTransactionObjects = (rawTransactions, options = {}) => {
    let out = [];

    rawTransactions.forEach((rawItem) => {
        const item = {
            name: "",
            amount: "",
            date: "",
            details: [],
            location: "",
            type: "",
            currency: options.currency,
        };

        if (Object.keys(rawItem).length > 0) {
            item.name = rawItem.type;
            item.amount = rawItem.price;
            item.date = rawItem.date;
            item.details = rawItem.details;
            item.location = rawItem.location;
            item.type = getTransactionTypeByLabel(rawItem.type);

            out.push(item);
        }
    });

    return out;
};

// @desc    Extracts transactions from the statement lines
// @return  The list of transactions, in json format
const extractTransactionsFromLines = (lines, options = {}) => {
    let transactions = [];
    let newTransaction = true; // first line is always a starter line
    let currentTransaction = {};

    for (let i = 0; i < lines.length; i++) {
        const currentLine = lines[i];
        const nextLine = lines[i + 1] ? lines[i + 1] : null;

        if (newTransaction) {
            currentTransaction = {};
            currentTransaction.type = getTransactionType(currentLine);
            currentTransaction.details = [];

            if (
                [
                    TAXE_SI_COMISIOANE,
                    COMISION_OPERATIUNE,
                    CUMPARARE_POS,
                    TRANSFER_HOME_BANK,
                    TRANSFER_TERT,
                    RATA_CREDIT,
                    ING_ASIGURARE_CREDIT,
                    RETRAGERE_NUMERAR,
                ].includes(currentTransaction.type)
            ) {
                let price = currentLine.split(currentTransaction.type)[0];
                currentTransaction.price = getPriceFloatValue(price);
                currentTransaction.date = parseRomanianDate(
                    currentLine.split(currentTransaction.type)[1]
                );
            }

            if ([INCASARE].includes(currentTransaction.type)) {
                currentTransaction.price = null;
                let currentTransactionDate = currentLine.split(
                    currentTransaction.type
                )[1];
                currentTransactionDate = currentTransactionDate.replace(
                    " via card",
                    ""
                );
                currentTransaction.date = parseRomanianDate(
                    currentTransactionDate
                );
            }
        } else {
            //   if (currentLine.includes(LOCATION_FLAG)) {
            if (LOCATION_FLAGS.some((value) => currentLine.includes(value))) {
                let foundValue = null;

                // Get the type of Location
                LOCATION_FLAGS.some((value) => {
                    if (currentLine.includes(value)) {
                        foundValue = value;
                        return true; // Stop iterating once a match is found
                    }
                    return false; // Continue iterating
                });

                currentTransaction.location = currentLine.split(foundValue)[1];
            } else {
                currentTransaction.details.push(currentLine);
            }
        }

        // End of the line
        if (!nextLine) {
            if ([INCASARE].includes(currentTransaction.type)) {
                let price = currentLine; // the price is the only item on this line
                currentTransaction.price = getPriceFloatValue(price);

                currentTransaction.details.length =
                    currentTransaction.details.length - 1;
            }

            transactions.push(currentTransaction);
            break;
        }

        // Prepare the next line
        newTransaction = isTransactionHeaderLine(nextLine);

        if (newTransaction) {
            // handle special cases for INCASARE
            if ([INCASARE].includes(currentTransaction.type)) {
                let price = currentLine; // the price is the only item on this line
                currentTransaction.price = getPriceFloatValue(price);
                currentTransaction.details.length =
                    currentTransaction.details.length - 1;
            }
            transactions.push(currentTransaction);
        }
    }

    return formatIngTransactionObjects(transactions, options);
};

module.exports = {
    getStatementBank,
    extractCurrency,
    parseTransactions,
    extractInitialBalance,
    extractFinalBalance,
    extractIban,
    extractStatementEarliestDate,
};
