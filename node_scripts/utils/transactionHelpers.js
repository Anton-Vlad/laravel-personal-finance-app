const INCOME_LABELS = [
    // ING labels
    "Incasare",

    // BT labels
    "Incasare OP",
    "Incasare Instant",
    "Depunere numerar ATM",
];

const EXPENSE_LABELS = [
    // ING labels
    "Cumparare POS",
    "Taxe si comisioane",
    "Comision pe operatiune",
    "Transfer Home'Bank",
    "Retragere numerar",
    "Transfer Tert Prestator Servicii Plata",
    "Rata Credit",
    "Prima asigurare ING Credit Protect",

    // BT labels
    "Plata la POS",
    "Plata la POS non-BT cu card VISA",
    "Retragere de numerar de la ATM BT",
    "Comision incasare OP",
    "Rambursare principal credit",
    "Dobanda credit",
    "Abonament BT 24",
    "Plata OP intra - canal electronic",
    "365",
];

const getTransactionTypeByLabel = (label) => {
    if (INCOME_LABELS.includes(label.trim())) {
        return "income";
    }
    if (EXPENSE_LABELS.includes(label.trim())) {
        return "expense";
    }
};

const isNumericValue = (str) => {
    if (typeof str != "string") return false;
    str = str.replace(",", "");
    return !isNaN(str) && !isNaN(parseFloat(str));
};

const getNumericValue = (str) => {
    if (!isNumericValue(str)) {
        return null;
    }

    str = str.replace(",", "");
    return parseFloat(str);
};

module.exports = {
    INCOME_LABELS,
    EXPENSE_LABELS,
    getTransactionTypeByLabel,
    isNumericValue,
    getNumericValue,
};
