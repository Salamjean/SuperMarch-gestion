<!-- Profile View -->
<main class="pos-center" id="view-profile"
    style="display: none; background: #f8fafc; flex-direction: column; overflow-y: auto; gap: 25px;">
    <!-- Header -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
        <div>
            <h2
                style="font-size: 24px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-user-gear"></i> Mon Profil Utilisateur
            </h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Gérez vos informations personnelles et
                mettez à jour votre mot de passe.</p>
        </div>
    </div>

    <!-- Main Profile Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
        <!-- Left Column: Avatar & Basic Employee Info -->
        <div
            style="background: #fff; border: 1px solid var(--border); border-radius: 20px; padding: 30px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div
                style="width: 110px; height: 110px; background: rgba(0, 77, 153, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; box-shadow: 0 10px 15px -3px rgba(0, 77, 153, 0.15);">
                <i class="fa-solid fa-user" style="font-size: 48px; color: var(--primary);"></i>
            </div>

            <div>
                <h3 id="profile-display-name"
                    style="font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 4px;">
                    {{ auth()->user()->name }}</h3>
                <div
                    style="font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ auth()->user()->position ?? 'Caissier / Caisse' }}</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Département:
                    {{ auth()->user()->department ?? 'Ventes' }}</div>
            </div>

            <!-- Meta Badges / Dates -->
            <div
                style="width: 100%; border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; flex-direction: column; gap: 12px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                    <i class="fa-solid fa-calendar-day" style="color: var(--text-muted); width: 16px;"></i>
                    <span style="color: var(--text-muted);">Embauché le :</span>
                    <span
                        style="font-weight: 700; color: var(--text);">{{ auth()->user()->hire_date ? \Carbon\Carbon::parse(auth()->user()->hire_date)->format('d F Y') : 'N/A' }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                    <i class="fa-solid fa-id-card" style="color: var(--text-muted); width: 16px;"></i>
                    <span style="color: var(--text-muted);">Rôle d'accès :</span>
                    <span
                        style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">{{ auth()->user()->role ?? 'Employé' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Form -->
        <div
            style="background: #fff; border: 1px solid var(--border); border-radius: 20px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <form id="profile-update-form" onsubmit="submitProfileUpdate(event)">
                @csrf
                <h3
                    style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Informations du Compte
                </h3>

                <!-- Personnal Information Fields -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div style="grid-column: 1 / -1; background: #eff6ff; padding: 12px 18px; border-radius: 10px; border: 1px dashed #3b82f6; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        <span style="font-size: 13.5px; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-id-badge" style="color: #3b82f6;"></i> Votre Code ID de Connexion :</span>
                        <span style="font-size: 16px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px;">{{ auth()->user()->login_code }}</span>
                    </div>
                    <div>
                        <label for="prof_name"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Nom
                            Complet</label>
                        <input type="text" id="prof_name" name="name" required value="{{ auth()->user()->name }}"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="prof_email"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Adresse
                            Email</label>
                        <input type="email" id="prof_email" name="email" required
                            value="{{ auth()->user()->email }}"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="prof_phone"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Numéro
                            de Téléphone</label>
                        <input type="text" id="prof_phone" name="phone" value="{{ auth()->user()->phone }}"
                            placeholder="Ex: +225 07 00 00 00"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="prof_gender"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Genre
                            (Sexe)</label>
                        <select id="prof_gender" name="gender"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; cursor: pointer;">
                            <option value="">Sélectionner</option>
                            <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Homme /
                                Masculin</option>
                            <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Femme /
                                Féminin</option>
                            <option value="other" {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Autre
                            </option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label for="prof_address"
                        style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Adresse
                        Domicile</label>
                    <input type="text" id="prof_address" name="address" value="{{ auth()->user()->address }}"
                        placeholder="Ex: Cocody Riviera Palmeraie, Abidjan"
                        style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                </div>

                <!-- Password Update Fields -->
                <h3
                    style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-top: 30px;">
                    <i class="fa-solid fa-lock" style="color: var(--danger);"></i> Modifier le mot de passe (Optionnel)
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label for="prof_password"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Nouveau
                            Mot de Passe</label>
                        <input type="password" id="prof_password" name="password" placeholder="Min. 6 caractères"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label for="prof_password_confirm"
                            style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Confirmer
                            Nouveau Mot de Passe</label>
                        <input type="password" id="prof_password_confirm" name="password_confirmation"
                            placeholder="Répéter le mot de passe"
                            style="width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit"
                        style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 77, 153, 0.2);"
                        onmouseover="this.style.background='var(--primary-light)'; this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.background='var(--primary)'; this.style.transform='none'">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
