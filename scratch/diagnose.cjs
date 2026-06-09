const { DatabaseSync } = require('node:sqlite');
const path = require('path');

try {
    const dbPath = path.join(__dirname, '..', 'database', 'database_local.sqlite');
    console.log('Lecture de la base de données :', dbPath);
    
    const db = new DatabaseSync(dbPath);
    
    const tables = ['users', 'categories', 'suppliers', 'products', 'customers', 'settings', 'cash_sessions', 'sales', 'sync_queue'];
    
    console.log('\n--- CONTENU DE LA BASE SQLITE LOCALE ---');
    for (const table of tables) {
        try {
            const count = db.prepare(`SELECT COUNT(*) as count FROM ${table}`).get().count;
            console.log(`Table "${table}" : ${count} ligne(s)`);
            if (table === 'users' && count > 0) {
                const users = db.prepare(`SELECT id, name, email, role FROM users`).all();
                console.log('  Utilisateurs :', users);
            }
        } catch (err) {
            console.log(`Table "${table}" : Erreur ou n'existe pas (${err.message})`);
        }
    }
} catch (e) {
    console.error('Erreur globale de diagnostic :', e);
}
