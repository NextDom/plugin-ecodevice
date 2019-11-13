FAQ
===

Il faut dans le menu gauche cliquer sur l’icône suivant pour y avoir
accès : ![la](../images/acces_sous_indicateur.jpg)

Par défaut c’est toutes les minutes. Il est possible de configurer
manuellement du push sur l’ecodevice. Pour les débits, consommations
instantanées et puissance apparente peuvent être collectées plus
fréquemment en fonction du démon.

Il faut cliquer sur l’icône à gauche du nom de la carte dans la liste de
gauche.

Ce plugin est gratuit pour que chacun puisse en profiter simplement. Si
vous souhaitez tout de même faire un don au développeur du plugin, merci
de m’envoyer un [message
privé](https://www.jeedom.com/forum/memberlist.php?mode=viewprofile&u=698)
sur le forum.

C’est tout à fait possible via
[github](https://github.com/guenneguezt/plugin-ecodevice)

Pour le calcul du débit de fuel en une heure de fonctionnement, il faut
connaître le marquage de votre gicleur de fuel. Pour cela, vous
trouverez les informations dans [le document suivant](http://fr.cd.danfoss.com/PCMPDF/DKBDPD060A204.pdf).

La valeur donnée est en USgal/Heure avec la correspondance en Kg/H.

Pour la densité du fuel, on peut prendre 820Kg/m³ et une pression de 7
bar.

Donc si vous avez un gicleur marqué 0.65S : 2,67 kg/h (suivant le
tableau Danfoss). 2,67x0,82=2,1894 litres à l’heure. Cela donne une
indication "approximative" de votre consommation.

Oui, il n’a pas été rédigé par mes soins, mais a le mérite d’exister.
Merci au rédacteur.
<http://blog.domadoo.fr/guides/jeedom-guide-dutilisation-plugin-ecodevice/>
