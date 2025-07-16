// Funcionalidades adicionales de JavaScript
document.addEventListener("DOMContentLoaded", function () {
  // Configurar fechas por defecto en el reporte de ventas
  const today = new Date();
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

  document.getElementById("start_date").valueAsDate = firstDay;
  document.getElementById("end_date").valueAsDate = today;

  // Otras funcionalidades pueden agregarse aquí
});
