const fs = require("fs");
const vm = require("vm");

try {
    const content = fs.readFileSync(
        "resources/views/employee/dashboard.blade.php",
        "utf8",
    );

    // Extract script tags content
    const scriptRegex = /<script>([\s\S]*?)<\/script>/gi;
    let match;
    let index = 1;

    while ((match = scriptRegex.exec(content)) !== null) {
        const scriptCode = match[1];
        try {
            // Try to parse the script block
            new vm.Script(scriptCode);
            console.log(`Script block ${index} parsed successfully!`);
        } catch (err) {
            console.error(`\n--- SYNTAX ERROR IN SCRIPT BLOCK ${index} ---`);
            console.error(err.message);

            // Find coordinates of the error in the original file
            const errorOffset = match.index + match[0].indexOf(scriptCode);
            const contentBeforeError = content.slice(0, errorOffset);
            const parentLineNum = contentBeforeError.split("\n").length;

            console.error(
                `Error starts around line ${parentLineNum} in dashboard.blade.php`,
            );

            // Print surrounding error context
            if (err.stack) {
                const lineMatch = err.stack.match(
                    /evalmachine\.<anonymous>:(\d+)/,
                );
                if (lineMatch) {
                    const relativeLineNum = parseInt(lineMatch[1]);
                    const scriptLines = scriptCode.split("\n");
                    console.error(`Local script line: ${relativeLineNum}`);
                    console.error("Surrounding code:");
                    const start = Math.max(0, relativeLineNum - 5);
                    const end = Math.min(
                        scriptLines.length,
                        relativeLineNum + 5,
                    );
                    for (let i = start; i < end; i++) {
                        console.error(
                            `${parentLineNum + i}: ${scriptLines[i]}`,
                        );
                    }
                }
            }
        }
        index++;
    }
} catch (e) {
    console.error(e);
}
