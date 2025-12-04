<?php
/**
 * Footer do Sistema
 */
?>
    </div>
    <!-- /.content-wrapper -->
    
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Ateliê Orçamentos</a>.</strong>
        Todos os direitos reservados.
        <div class="float-right d-none d-sm-inline-block">
            <b>Versão</b> 1.0.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- InputMask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

<script>
    // Configurações globais
    $(document).ready(function() {
        // DataTables em português
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
            },
            responsive: true,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]]
        });
        
        // Select2 em português
        $.fn.select2.defaults.set('language', 'pt-BR');
        $.fn.select2.defaults.set('theme', 'bootstrap4');
        
        // Máscaras
        $('.telefone').inputmask('(99) 9999-9999');
        $('.celular').inputmask('(99) 99999-9999');
        $('.cpf').inputmask('999.999.999-99');
        $('.cnpj').inputmask('99.999.999/9999-99');
        $('.cep').inputmask('99999-999');
        $('.dinheiro').inputmask('currency', {
            prefix: 'R$ ',
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0'
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirmação de exclusão
        $('.btn-delete').on('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir este registro?')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Função para formatar moeda
    function formatMoney(value) {
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Função para converter moeda para float
    function parseMoney(value) {
        if (typeof value === 'number') return value;
        return parseFloat(value.replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0;
    }
</script>

<?php if (isset($extraJS)) echo $extraJS; ?>

</body>
</html>
