<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Geo Admin') }} - Mapa</title>
    
    <!-- Load Calcite Design System -->
    <script type="module" src="https://js.arcgis.com/calcite-components/3.3.3/calcite.esm.js"></script>
    
    <!-- Load the JavaScript Maps SDK core API -->
    <script src="https://js.arcgis.com/4.34/"></script>
    
    <!-- Load the JavaScript Maps SDK Map components -->
    <script type="module" src="https://js.arcgis.com/4.34/map-components/"></script>
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        arcgis-map {
            width: 100%;
            height: 100vh;
            display: block;
        }
        
        .loading-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            color: #333;
        }
        
        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #f44336;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            display: none;
        }
    </style>
</head>
<body>
    <arcgis-map basemap="streets">
        <arcgis-zoom slot="top-left"></arcgis-zoom>
    </arcgis-map>
    
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <span>Carregando mapa e camadas...</span>
    </div>
    
    <div id="errorMessage" class="error-message"></div>
    
    <!-- Passar API key do Laravel para JavaScript -->
    <script>
        window.arcgisApiKey = @json(config('arcgis.api_key', ''));
    </script>
    
    <script type="module">
        function hideLoading() {
            const overlay = document.getElementById("loadingOverlay");
            if (overlay) {
                overlay.style.display = "none";
            }
        }
        
        function showError(message) {
            const errorDiv = document.getElementById("errorMessage");
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = "block";
                setTimeout(() => {
                    errorDiv.style.display = "none";
                }, 5000);
            }
        }
        
        // Timeout de segurança global
        setTimeout(function() {
            const overlay = document.getElementById("loadingOverlay");
            if (overlay && overlay.style.display !== "none") {
                console.warn("Timeout: escondendo loading após 10 segundos");
                hideLoading();
            }
        }, 10000);
        
        // Verificar se o ArcGIS está disponível
        if (typeof $arcgis === 'undefined') {
            console.error("ArcGIS SDK não carregou");
            showError("Erro: Biblioteca ArcGIS não carregou. Verifique sua conexão com a internet.");
            hideLoading();
        } else {
            document.addEventListener('DOMContentLoaded', async function() {
                try {
                    const esriConfig = await $arcgis.import("@arcgis/core/config.js");
                    if (window.arcgisApiKey) {
                        esriConfig.apiKey = window.arcgisApiKey;
                        console.log("API Key configurada");
                    } else {
                        console.warn("API Key não configurada no .env");
                    }
                    
                    const GeoJSONLayer = await $arcgis.import("@arcgis/core/layers/GeoJSONLayer.js");
                    const Legend = await $arcgis.import("@arcgis/core/widgets/Legend.js");
                    const SimpleRenderer = await $arcgis.import("@arcgis/core/renderers/SimpleRenderer.js");
                    const SimpleFillSymbol = await $arcgis.import("@arcgis/core/symbols/SimpleFillSymbol.js");
                    const SimpleLineSymbol = await $arcgis.import("@arcgis/core/symbols/SimpleLineSymbol.js");
                    
                    const mapElement = document.querySelector("arcgis-map");
                    
                    if (!mapElement) {
                        console.error("Componente arcgis-map não encontrado!");
                        showError("Erro: Componente do mapa não encontrado.");
                        hideLoading();
                        return;
                    }
                    
                    console.log("Aguardando view estar pronta...");
                    
                    let view = null;
                    let attempts = 0;
                    const maxAttempts = 50;
                    
                    while (!view && attempts < maxAttempts) {
                        if (mapElement.view) {
                            view = mapElement.view;
                            console.log("View encontrada diretamente");
                            break;
                        }
                        
                        await new Promise(resolve => setTimeout(resolve, 100));
                        attempts++;
                    }
                    
                    if (!view) {
                        try {
                            await mapElement.viewOnReady();
                            view = mapElement.view;
                            console.log("View obtida via viewOnReady()");
                        } catch (e) {
                            console.warn("viewOnReady também falhou:", e);
                            await new Promise(resolve => setTimeout(resolve, 1000));
                            view = mapElement.view;
                        }
                    }
                    
                    if (!view) {
                        throw new Error("Não foi possível obter a view do mapa após múltiplas tentativas");
                    }
                    
                    await view.when();
                    
                    console.log("Mapa carregado com sucesso");
                    console.log("View ready, center:", view.center, "zoom:", view.zoom);
                    
                    hideLoading();
                    
                    view.goTo({
                        center: [-47.8825, -15.7942],
                        zoom: 4
                    });
                    
                    async function loadLayers() {
                        try {
                            const response = await fetch('/api/layers');
                            
                            if (!response.ok) {
                                throw new Error(`Erro ${response.status}: ${response.statusText}`);
                            }
                            
                            const geoJSON = await response.json();
                            
                            if (!geoJSON.features || geoJSON.features.length === 0) {
                                console.log("Nenhuma camada encontrada no banco de dados");
                                hideLoading();
                                return;
                            }
                            
                            console.log(`Carregando ${geoJSON.features.length} camada(s)...`);
                            
                            // Criar camada GeoJSON
                            const geoJSONLayer = new GeoJSONLayer({
                                url: URL.createObjectURL(new Blob([JSON.stringify(geoJSON)], {
                                    type: "application/json"
                                })),
                                title: "Camadas Geográficas",
                                renderer: new SimpleRenderer({
                                    symbol: new SimpleFillSymbol({
                                        color: [226, 119, 40, 0.5], // Cor laranja semi-transparente
                                        outline: new SimpleLineSymbol({
                                            color: [226, 119, 40, 0.8],
                                            width: 2
                                        })
                                    })
                                }),
                                popupTemplate: {
                                    title: "Camada: {name}",
                                    content: [{
                                        type: "fields",
                                        fieldInfos: [{
                                            fieldName: "name",
                                            label: "Nome"
                                        }, {
                                            fieldName: "created_at",
                                            label: "Criado em"
                                        }]
                                    }]
                                }
                            });
                            
                            mapElement.map.add(geoJSONLayer);
                            geoJSONLayer.when(function() {
                                console.log("Camada GeoJSON carregada com sucesso");
                                
                                if (geoJSONLayer.fullExtent) {
                                    view.goTo(geoJSONLayer.fullExtent).catch(function(extentError) {
                                        console.warn("Erro ao ajustar extensão:", extentError);
                                        hideLoading();
                                    }).then(function() {
                                        hideLoading();
                                    });
                                } else {
                                    hideLoading();
                                }
                            }).catch(function(layerError) {
                                console.error("Erro ao carregar camada GeoJSON:", layerError);
                                showError("Erro ao carregar camada: " + (layerError.message || layerError));
                                hideLoading();
                            });
                            
                            const legend = new Legend({
                                view: view,
                                layerInfos: [{
                                    layer: geoJSONLayer,
                                    title: "Camadas"
                                }]
                            });
                            
                            view.ui.add(legend, "bottom-left");
                            
                        } catch (error) {
                            console.error("Erro ao carregar camadas:", error);
                            showError("Erro ao carregar camadas: " + (error.message || error));
                            hideLoading();
                        }
                    }
                    
                    setTimeout(function() {
                        loadLayers();
                    }, 500);
                    
                } catch (error) {
                    console.error("Erro ao inicializar o mapa:", error);
                    showError("Erro ao inicializar o mapa: " + (error.message || error));
                    hideLoading();
                }
            });
        }
    </script>
</body>
</html>
