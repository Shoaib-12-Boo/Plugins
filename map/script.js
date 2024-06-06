document.addEventListener("DOMContentLoaded", function() {
    const regions = {
      caltanissetta: {
        name: "CALTANISSETTA",
        description: "Visitare l’entroterra siculo offre l’opportunità di scoprire qualcosa di nuovo su quest’isola. Natura, arte, storia, gastronomia sono i quattro punti cardinali le cui coordinate puntano dritte su Caltanissetta.",
        link: "#"
      },
      agrigento: {
        name: "AGRIGENTO",
        description: "Agrigento è tra le città più antiche della Sicilia ed è risorta più volte sulle sue antiche vestigia.  La Valle dei Templi ci racconta uno dei sui volti più affascinanti, legato al mondo classico, insieme agli straordinari reperti custoditi nel Museo Archeologico Regionale.",
        link: "#"
      },
      catania: {
        name: "CATANIA",
        description: "Catania, seconda città metropolitana della Sicilia, ha dovuto affrontare nei secoli terribili terremoti ed eruzioni vulcaniche, ha subito alternanze di dominazioni e trasformazioni urbanistiche.",
        link: "#"
      }
      ,
      messina: {
        name: "MESSINA",
        description: "I greci si stabiliscono in questo angolo di terra chiamandolo Zancle che significa falce, dalla forma del suo porto naturale. Il suo nome attuale deriva da Messenia,  la cittadina greca dei coloni che la popolano successivamente.",
        link: "#"
      }
      ,
      enna: {
        name: "ENNA",
        description: "Il panorama di Enna, il capoluogo più alto d’Europa, vi incanterà da qualsiasi punto della città vi affacciate per la bellezza della natura che la circonda.",
        link: "#"
      }
      ,
      trapani: {
        name: "TRAPANI",
        description: "Trapani, con la sua la forma a falce, dal greco Drepanon, gode di una posizione geografica invidiabile: tra due mari, ai piedi del Monte Erice, vicino Selinunte, di fronte le splendide Isole Egadi e le famose Saline.",
        link: "#"
      }
      ,
      siracusa: {
        name: "SIRACUSA",
        description: "La sua storia millenaria, la Neapolis, Ortigia, i papiri del Ciane, la magia e il fascino dei suoi territori e delle sue coste, Noto, Vendìcari, Marzamemi, fino all'Isola delle Correnti, ne fanno una delle mete più ambite di chi visita la Sicilia.",
        link: "#"
      },
      ragusa: {
        name: "RAGUSA",
        description: "La sua storia millenaria, la Neapolis, Ortigia, i papiri del Ciane, la magia e il fascino dei suoi territori e delle sue coste, Noto, Vendìcari, Marzamemi, fino all'Isola delle Correnti, ne fanno una delle mete più ambite di chi visita la Sicilia.",
        link: "#"
      },
      palermo: {
        name: "PALERMO",
        description: "Palermo è una delle città più affascinanti d’Europa: tra le vie del suo centro storico scoprirete i luoghi più incredibili.",
        link: "#"
      }
      // Add more regions here
    };
  
    const info = document.getElementById('info');
    const regionName = document.getElementById('region-name');
    const regionDescription = document.getElementById('region-description');
    const regionLink = document.getElementById('region-link');
  
    d3.selectAll('.region').on('click', function(event) {
      const regionId = d3.select(this).attr('id');
      const regionData = regions[regionId];
  
      // Update the information panel
      regionName.textContent = regionData.name;
      regionDescription.textContent = regionData.description;
      regionLink.href = regionData.link;
  
      // Highlight the selected region
      d3.selectAll('.region').classed('im-active', false);
      d3.select(this).classed('im-active', true);
    });
  });
  