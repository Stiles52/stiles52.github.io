<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">

    <div id="view-history" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Gérer vos tickets</h3>

        <div class="mt-8">
            <div class="flex justify-end m-4">
                <input type="text" style="width: 400px;" placeholder="Recherche">
                <button class="origin-btn btn--graphic btn--primary">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
                <button class="dropdown origin-btn btn--full-graphic btn--primary ml-8">
                    Filtres
                    <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                </button>
                <a href="lore.html" class="dropdown origin-btn btn--full-graphic btn--success ml-8">
                    Créer un ticket
                    <i data-lucide="circle-plus" class="btn--icon"></i>
                </a>
            </div>
            <div class="dropdown-container hidden w-full flex p-8 gap-4">
                <div style="width: 350px;">
                    <label>Statut</label>
                    <select style="width: 100%;">
                        <option>Tous</option>
                        <option>Nouveau</option>
                        <option>En cours</option>
                        <option>En attente</option>
                        <option>Résolut</option>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Tags</label>
                    <select style="width: 100%;">
                        <option>Tous</option>
                        <option>Priorité basse</option>
                        <option>Priorité moyenne</option>
                        <option>Priorité haute</option>
                        <option>...</option>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Ordre d'apparition</label>
                    <select style="width: 100%;">
                        <option>Réponse la plus récente</option>
                        <option>Réponse la moins récente</option>
                        <option>Ordre alphabetique</option>
                        <option>Ordre de création</option>
                        <option>...</option>
                    </select>
                </div>
            </div>
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
        </div>

        <div class="flex justify-end mt-8 mb-24 gap-4">
            <a href="lore.html" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="arrow-big-left" class="btn--icon"></i>
            </a>
            <a href="lore.html" class="origin-btn btn--full-graphic btn--primary">
                <span>1</span>
            </a>
            <a href="lore.html" class="origin-btn btn--full-graphic btn--primary">
                <span>2</span>
            </a>
            <a href="lore.html" class="origin-btn btn--full-graphic btn--primary">
                <span>3</span>
            </a>
            <a href="lore.html" class="origin-btn btn--full-graphic btn--primary">
                <span>4</span>
            </a>
            <a href="lore.html" class="origin-btn btn--full-graphic btn--secondary">
                <span>...</span>
            </a>
            <a href="lore.html" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="arrow-big-right" class="btn--icon"></i>
            </a>
        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>