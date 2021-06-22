</div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                All Rights Reserved by Technoilahi.
            </footer>
          
    <script src="<?=base_url()?>assets/belakang/design/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>assets/belakang/design/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?=base_url()?>assets/belakang/design/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=base_url()?>assets/belakang/design/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="<?=base_url()?>assets/belakang/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="<?=base_url()?>assets/belakang/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="<?=base_url()?>assets/belakang/dist/js/custom.min.js"></script>


	<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
	<script src="<?=base_url()?>assets/js/chart.min.js"></script>

	<script src="<?=base_url()?>assets/js/easypiechart.js"></script>

	<script src="<?=base_url()?>assets/js/bootstrap-datepicker.js"></script>
	<script>
		$('#calendar').datepicker({
		});

		!function ($) {
		    $(document).on("click","ul.nav li.parent > a > span.icon", function(){          
		        $(this).find('em:first').toggleClass("glyphicon-minus");      
		    }); 
		    $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
		}(window.jQuery);

		$(window).on('resize', function () {
		  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
		})
		$(window).on('resize', function () {
		  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
		})
	</script>	


  <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>

</body>
</html>