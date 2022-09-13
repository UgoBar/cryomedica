//--- HTML NODES ---//
const preOrderHtml = `
    <section class="step next-step" id="pre-order">
        <h3>Pré-réserver</h3>
        <p class="text-center">Pour pré-réserver une ou plusieurs séance(s) merci de remplir le formulaire ci dessous</p>
        <div class="inner-form">
            <div class="form-group">
                <input id="firstname" name="firstname" type="text" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <input id="lastname" name="lastname" type="text" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <input id="phone" name="phone" type="text" placeholder="Téléphone" required>
            </div>
            <div class="form-group">
                <input id="email" name="email" type="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input id="number" name="number" type="number" min="1" max="500" placeholder="Choix du nombre de séance(s)" required>
                <label for="number"></label>
            </div>
        </div>
    </section>
`

const partnerHtml = `
    <section class="step next-step" id="partner">
        <h3>Partenaire</h3>
        <p class="become-partner-info">Pour devenir partnaire merci de compléter le formulaire ci-dessous, nous reviendrons vers vous le plus vite possible.</p>
        <div class="inner-form">
            <div class="form-group">
                <input id="firstname" name="firstname" type="text" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <input id="lastname" name="lastname" type="text" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <input id="company" name="company" type="text" placeholder="Entreprise" required>
            </div>
            <div class="form-group">
                <input id="phone" name="phone" type="text" placeholder="Téléphone" required>
            </div>
            <div class="form-group">
                <input id="email" name="email" type="email" placeholder="Email" required>
            </div>
        </div>
    </section>
`

const interestHtml = `
    <section class="step next-step" id="interest">
        <h3>Marque d'intérêt</h3>
        <p class="text-center">Ce centre n'est pas encore ouvert. Cependant vous pouvez nous laisser vos coordonnées pour nous faire part de votre marque d'intérêt.</p>
        <div class="inner-form">
            <div class="form-group">
                <input id="firstname" name="firstname" type="text" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <input id="lastname" name="lastname" type="text" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <input id="phone" name="phone" type="text" placeholder="Téléphone" required>
            </div>
            <div class="form-group">
                <input id="email" name="email" type="email" placeholder="Email" required>
            </div>
        </div>
    </section>
`
