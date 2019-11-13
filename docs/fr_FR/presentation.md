Présentation
============

Ce plugin permet de gérer les cartes Ecodevice de GCE.

Initialement, ce plugin a été créé pour connecter les Ecodevices de GCE.

Données visibles sur le Dashboard :
-----------------------------------

-   *''les compteurs de Téléinformation '*'

![](../images/ecodevice_screenshot2.jpg)

-   *''les compteurs d’impulsions*''

![](../images/ecodevice_screenshot3.jpg)

Fréquence de rafraichissement
-----------------------------

Le plugin met à jour ces données de 2 façons :

Les données sont récupérées par le plugin toutes les minutes pour tous
les compteurs. Il n’est pas possible d’avoir une fréquence plus élevée.

Pour les débits, consommations instantanés et Puissance Apparente une
collecte est fait par un daemon en fonction de la fréquence paramétrée
au niveau du plugin. Attention, une forte diminution engendre une forte
surcharge de votre serveur jeedom.
