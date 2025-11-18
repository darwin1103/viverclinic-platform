<div class="modal fade" id="modalRate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-star-half me-2"></i>
                    Califica tu experiencia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="POST" action="" id="ratingForm">
                @csrf

                <input type="hidden" name="session_number" id="ratingSessionNumber">
                <input type="hidden" name="rating_value" id="ratingValueInput">

                <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="https://images.unsplash.com/photo-1607746882042-944635dfe10e?q=80&w=160&auto=format&fit=crop"
                             class="rounded-circle"
                             width="56"
                             height="56"
                             alt="Profesional">
                        <div>
                            <div class="fw-semibold" id="ratingSpecialistName">{{ $specialist->name ?? 'Especialista' }}</div>
                            <div class="text-secondary small" id="ratingTreatmentName">
                                {{ $treatment->name ?? 'Tratamiento' }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">¿Cómo fue tu experiencia?</label>
                        <div class="faces justify-content-center" id="faces">
                            <div class="face good" data-value="3" title="Excelente 😊">
                                😊
                            </div>
                            <div class="face neu" data-value="2" title="Normal 😐">
                                😐
                            </div>
                            <div class="face bad" data-value="1" title="Mala 😞">
                                😞
                            </div>
                        </div>
                        <div class="bars">
                            <div class="bar green"></div>
                            <div class="bar yellow"></div>
                            <div class="bar red"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="rateComment" class="form-label">
                            Comentario (opcional)
                        </label>
                        <textarea
                            id="rateComment"
                            name="comment"
                            rows="3"
                            class="form-control"
                            placeholder="Cuéntanos cómo te fue…"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" id="btnSendRate" class="btn btn-primary" disabled>
                        Enviar calificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
