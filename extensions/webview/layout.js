document.addEventListener('DOMContentLoaded', () => {
    const iframe = document.querySelector('#body iframe');
    const navButtons = document.querySelectorAll('#nav-menu button');
    const timestampEl = document.getElementById('timestamp');
    const splitBtn = document.getElementById('split-btn');

    // Charger la vue demandée
    const loadView = (view) => {
        iframe.src = `${view}.html`;
        navButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`#nav-menu button[data-view="${view}"]`).classList.add('active');
        localStorage.setItem('lastView', view);
    };

    // Navigation par clic
    navButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            loadView(btn.dataset.view);
        });
    });

    // Charger la dernière vue sauvegardée
    const lastView = localStorage.getItem('lastView') || 'chat';
    loadView(lastView);

    // Mettre à jour le footer en temps réel
    const updateFooter = () => {
        fetch('/api/server/status')
            .then(r => r.json())
            .then(data => {
                const statusText = data.status === 'running' ? 'Connecté' : 'Déconnecté';
                timestampEl.textContent = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, '');
                document.querySelector('#footer span:first-child').textContent = 
                    `Serveur : ${statusText} • UTF-8 • Projet : — • Fichier : — • Type : —`;
            })
            .catch(() => {
                timestampEl.textContent = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, '');
                document.querySelector('#footer span:first-child').textContent = 
                    `Serveur : Déconnecté • UTF-8 • Projet : — • Fichier : — • Type : —`;
            });
    };

    setInterval(updateFooter, 2000);
    updateFooter();

    // Activer/désactiver Fractionner selon la largeur
    const toggleSplitBtn = () => {
        splitBtn.style.display = window.innerWidth >= 1024 ? 'block' : 'none';
    };
    toggleSplitBtn();
    window.addEventListener('resize', toggleSplitBtn);

    // Ajouter un bouton pour la liste d'attente dans le header (si non présent)
    const navMenu = document.getElementById('nav-menu');
    const queueBtn = document.createElement('button');
    queueBtn.id = 'queue-btn';
    queueBtn.innerHTML = '📋';
    queueBtn.title = 'Liste d’attente';
    queueBtn.className = 'clay-button';
    navMenu.appendChild(queueBtn);

    queueBtn.addEventListener('click', () => {
        document.getElementById('queue-panel').classList.toggle('active');
    });
});
