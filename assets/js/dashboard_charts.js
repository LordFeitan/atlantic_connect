document.addEventListener("DOMContentLoaded", function () {
  // Check if chartData is defined (passed from PHP)
  if (typeof chartData !== "undefined") {
    const ctx = document.getElementById("visitasChart").getContext("2d");

    // Gradient for bars
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, "#d4af37");
    gradient.addColorStop(1, "#b48f26");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: "Visitas",
            data: chartData.data,
            backgroundColor: gradient,
            borderColor: "#d4af37",
            borderWidth: 1,
            borderRadius: 5,
            barPercentage: 0.6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: true,
            text: "Visitas por Mes (Últimos 6 Meses)",
            color: "#333",
            font: {
              family: "Poppins",
              size: 16,
              weight: "600",
            },
            padding: {
              bottom: 20,
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(0, 0, 0, 0.05)",
            },
            ticks: {
              font: {
                family: "Poppins",
              },
            },
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              font: {
                family: "Poppins",
              },
            },
          },
        },
        animation: {
          duration: 2000,
          easing: "easeOutQuart",
        },
      },
    });
  }
});
