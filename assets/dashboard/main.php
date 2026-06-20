<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div id="view-history" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Dashboard</h3>

        <div style="display: flex; gap: 15px; justify-content: space-between; flex-wrap: wrap;">
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="tickets" class="w-6 h-6 text-cyan-400"></i> 999.999
                </h3>
                Nb. totaux de ticket ouvert
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-minus" class="w-6 h-6 text-orange-400"></i> 999.999
                </h3>
                Ticket en cours
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-plus" class="w-6 h-6 text-green-400"></i> 999.999
                </h3>
                Ticket attendant votre réponse
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-slash" class="w-6 h-6 text-red-400"></i> 999.999
                </h3>
                Ticket en attente du staff
            </div>
        </div>

        <div class="mt-8">
            <h4 class="pb-2">Vos tickets ayant eu la plus récente activité :</h4>
            <table>
                <thead>
                    <tr>
                        <td width="100px">ID#</td>
                        <td width="150px">Statut</td>
                        <td width="300px">Titre</td>
                        <td>Dernière réponse</td>
                        <td width="200px">Tags</td>
                        <td width="80px">Actions</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#0000001</td>
                        <td>
                            <div class="badge-glass badge-glass--primary">
                                <span class="badge-glass--text">Nouveau</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Priorité basse</span>
                            </div>
                            <div class="badge-glass badge-glass--warning mb-2">
                                <span class="badge-glass--text">Ticket Modération</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000002</td>
                        <td>
                            <div class="badge-glass badge-glass--warning">
                                <span class="badge-glass--text">En cours</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--warning mb-2">
                                <span class="badge-glass--text">Priorité moyenne</span>
                            </div>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Ticket Administration</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000003</td>
                        <td>
                            <div class="badge-glass badge-glass--secondary">
                                <span class="badge-glass--text">En attente</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000004</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000005</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000006</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000007</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000008</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000009</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>#0000010</td>
                        <td>
                            <div class="badge-glass badge-glass--success">
                                <span class="badge-glass--text">Résolut !</span>
                            </div>
                        </td>
                        <td>Le titre d'un ticket !</td>
                        <td>
                            <section class="card-holographic">
                                <div class="card-holographic--title">
                                    <i data-lucide="message-circle-warning" class="w-5 h-5 text-cyan-400"></i>
                                    <span>Réponse de : XXXXXXXXXXXXX</span>
                                </div>
                                <div>
                                    <div>
                                        <p>
                                            Lorem ipsum dolor sit amet. Et rerum odio qui consequatur maiores At nihil voluptatem non deserunt neque eum nesciunt soluta. Nam velit iste in tempore consequatur sed voluptates reprehenderit non nesciunt consequatur et error doloremque vel atque reprehenderit eos quas iste.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </td>
                        <td>
                            <div class="badge-glass badge-glass--danger mb-2">
                                <span class="badge-glass--text">Priorité haute</span>
                            </div>
                            <div class="badge-glass badge-glass--primary mb-2">
                                <span class="badge-glass--text">Ticket Scénariste</span>
                            </div>
                        </td>
                        <td>
                            <button class="dropdown">
                                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                <div class="dropdown-container dropdown-container--popup hidden">
                                    <a href="#">
                                        <i data-lucide="eye" class="btn--icon"></i>
                                        Voir le ticket
                                    </a>
                                    <a href="#">
                                        <i data-lucide="pen" class="btn--icon"></i>
                                        Éditer
                                    </a>
                                    <a href="#">
                                        <i data-lucide="ticket-x" class="btn--icon"></i>
                                        Fermer le ticket
                                    </a>
                                </div>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="flex justify-center pt-8 pb-24">
                <a href="lore.html" class="origin-btn btn--full-graphic btn--primary">
                    <span>Voir plus</span>
                    <i data-lucide="arrow-big-right" class="btn--icon"></i>
                </a>
            </div>
        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>