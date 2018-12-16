<?php include 'header.php'; ?>

<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Histórico de Transacciones

              <a href="<?php echo url() ?>" style="float: right;">Crear Transacción</a>
             </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="transacciones-table" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>ID Transacción</th>
                      <th>Estado</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer small text-muted"></div>
          </div>



<?php include 'footer.php'; ?>
<script src="<?php echo url('assets/js/datatables.js?v=1') ?>"></script>