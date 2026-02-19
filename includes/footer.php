<?php
/**
 * Footer Component - HYPE Sportswear
 * Contiene: footer, botón flotante de WhatsApp, modal de checkout, toast container, scripts
 */
?>

    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <a href="index.php" class="logo"><?php echo htmlspecialchars($site_logo); ?><span class="text-neon">.</span></a>
                <div class="social-links">
                    <?php if ($ig = getSetting('social_instagram')): ?>
                        <a href="<?php echo $ig; ?>" target="_blank"><i class="ph ph-instagram-logo"></i></a>
                    <?php endif; ?>
                    <?php if ($tk = getSetting('social_tiktok')): ?>
                        <a href="<?php echo $tk; ?>" target="_blank"><i class="ph ph-tiktok-logo"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="footer__bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name ?? 'HYPE Sportswear'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/<?php echo getSetting('whatsapp_number', '584120936783'); ?>" target="_blank" class="whatsapp-float" aria-label="Soporte por WhatsApp">
        <i class="ph ph-whatsapp-logo"></i>
    </a>

    <div class="checkout-overlay" id="checkoutOverlay">
        <div class="checkout-modal">
            <div class="checkout-header">
                <h3>FINALIZAR COMPRA</h3>
                <button class="close-btn" id="closeCheckout"><i class="ph ph-x"></i></button>
            </div>

            <div class="checkout-body">
                <div class="checkout-summary">
                    <div class="summary-row">
                        <span>Tasa BCV:</span>
                        <span class="text-neon" id="displayRate">Cargando...</span>
                    </div>
                    <div class="summary-row">
                        <span>Total USD:</span>
                        <span id="displayTotalUSD">$0.00</span>
                    </div>
                    <div class="summary-row total-bs">
                        <span>Total Bs:</span>
                        <span class="text-neon" id="displayTotalBS">Bs 0,00</span>
                    </div>
                </div>

                <form id="checkoutForm" class="checkout-form">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" id="clientName" placeholder="Tu nombre" required>
                    </div>
                    
                    <div class="form-row" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <input type="tel" id="clientPhone" placeholder="0412..." required>
                        </div>
                        <div class="form-group">
                            <label>Email (Opcional)</label>
                            <input type="email" id="clientEmail" placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Método de Pago</label>
                        <select id="paymentMethod" required onchange="updatePaymentInstructions()">
                            <option value="" disabled selected>Cargando métodos...</option>
                            <!-- Se llena dinámicamente con JS -->
                        </select>
                    </div>

                    <!-- Contenedor dinámico de instrucciones -->
                    <div id="paymentInstructions" style="background:#1A1B1E; padding:10px; border-radius:8px; margin-bottom:15px; font-size:0.9rem; color:#ccc; display:none; border: 1px solid #333;">
                        <!-- Aquí van los datos bancarios -->
                    </div>

                    <div class="form-group" id="referenceContainer">
                        <label>Referencia de Pago / Comprobante</label>
                        <input type="text" id="paymentReference" placeholder="Últimos 4 o 6 dígitos" required>
                    </div>

                    <div class="form-group">
                        <label>Dirección de Envío / Retiro</label>
                        <textarea id="clientAddress" rows="2" placeholder="Ej: MRW Plaza Venezuela o Retiro Personal" required></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary btn--full" id="btnSubmitOrder">
                        CONFIRMAR Y PAGAR <i class="ph ph-check-circle"></i>
                    </button>
                    <p id="orderStatus" style="text-align:center; margin-top:10px; font-size:0.9rem; color:#888;"></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>

</html>
