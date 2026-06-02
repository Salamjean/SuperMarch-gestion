const fs = require("fs");
const vm = require("vm");

try {
    let content = fs.readFileSync(
        "resources/views/employee/dashboard.blade.php",
        "utf8",
    );

    // Replace all Blade comments, unescaped tags and escaped tags with whitespace/dummy string
    // This allows the Javascript parser to compile the script block as clean JS.
    content = content.replace(/\{\{[\s\S]*?\}\}/g, '"blade_val"');
    content = content.replace(/\{!![\s\S]*?!!\}/g, '"blade_val"');
    content = content.replace(/@\w+[\s\S]*?\n/g, "\n"); // Remove directives like @csrf, etc.

    // Extract script tags content
    const scriptRegex = /<script>([\s\S]*?)<\/script>/gi;
    let match;
    let index = 1;
    let hasError = false;

    while ((match = scriptRegex.exec(content)) !== null) {
        const scriptCode = match[1];
        try {
            // Try to parse the script block
            new vm.Script(scriptCode);
            console.log(`Script block ${index} parsed successfully!`);
        } catch (err) {
            hasError = true;
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
                    const start = Math.max(0, relativeLineNum - 10);
                    const end = Math.min(
                        scriptLines.length,
                        relativeLineNum + 10,
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
    if (!hasError) {
        console.log("No syntax errors found in any JavaScript block!");
    }
} catch (e) {
    console.error(e);
}
