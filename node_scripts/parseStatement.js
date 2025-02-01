const path = require("path");
const fs = require("fs");
const pdf = require("pdf-parse");

const {
    getStatementBank,
    extractInitialBalance,
    extractFinalBalance,
    extractIban,
    extractCurrency,
    extractStatementEarliestDate,
    parseTransactions,
} = require("./utils/statementHelpers");



const parseStatement = async (filePath) => {
    let dataBuffer = fs.readFileSync(filePath); // will read the file from cdn server
    const fileData = await pdf(dataBuffer);

    const statementBank = getStatementBank(fileData.text);
    const earliestDate = extractStatementEarliestDate(fileData.text);
    const statementCurreny = extractCurrency(fileData.text);
    const statementInitialBalance = extractInitialBalance(
        fileData.text,
        statementBank
    );
    const statementFinalBalance = extractFinalBalance(
        fileData.text,
        statementBank
    );
    const statementIBAN = extractIban(fileData.text);

    const transactions = parseTransactions(fileData.text, {
        bank: statementBank,
        currency: statementCurreny,
        numpages: fileData.numpages
    });


    console.log(JSON.stringify({
        statementBank,
        statementCurreny,
        earliestDate,
        statementInitialBalance,
        statementFinalBalance,
        statementIBAN,
        transactions
    }));
}


// Add this code to handle command line arguments
if (require.main === module) {
    const filePath = process.argv[2];
    if (!filePath) {
        console.error("Please provide a file path as an argument.");
        process.exit(1);
    }
    parseStatement(filePath).catch(console.error);

    // parseStatement(
    //     path.join(__dirname, "../storage/app/private/statements/ING Bank - Extras de cont septembrie_2021_RO80INGB0000999904618126_RON.pdf")
    // ).catch(console.error);

}
