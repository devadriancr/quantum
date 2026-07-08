const XLSX = require('xlsx');

const [, , inputPath, outputPath] = process.argv;

if (!inputPath || !outputPath) {
    console.error('Uso: node convert.js <entrada.xlsb> <salida.xlsx>');
    process.exit(1);
}

const workbook = XLSX.readFile(inputPath);
XLSX.writeFile(workbook, outputPath, { bookType: 'xlsx' });
