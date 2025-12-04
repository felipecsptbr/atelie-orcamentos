<?php
// Teste simples
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

echo "Teste: " . formatCurrency(100.50);
?>
