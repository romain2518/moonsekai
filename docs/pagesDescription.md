# Description technique des pages du sites

## Liens utiles

<details>

- Cahier des charges
- Mocodo
- Charte des validité des champs utilisateurs
- Trello

</details>

<br>

## **Pages communes**

- ### Header

  Logo, cloche de notification avec menu déroulant intégré (et option recevoir par mail on/off ?), connexion, nav ?

- ### Footer

  Copyright, mentions légales, contact et autres fioritures...

- ### Home

  On retrouvera sur la page d'accueil une liste des dernières annonces d'actualité des oeuvres, une oeuvre random, un/plusieurs top etc. **(reste à définir)**.
  [Si un **admin** est connecté, on affichera le bouton d'ajout d'oeuvre]

- ### Recherche

  <details>
  
    - Par oeuvre (filtres à définir),
    - Par membre (avec boutons de gestion admin si admin connecté),
    - Par plateforme de visionnage/lecture.
  </details>

## **Espace membre**

- ### Formulaire d'inscription

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Pseudo | text | pseudo | ? | ? | Oui | Pseudo... | ---- |
    | Email | email | email | ? | ?35? | Oui | Email... | ---- |
    | Mdp | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
    | Mdp confirm | password | passwordConfirm | 8 | ?50? | Oui | Confirmation... | ---- |
  </details>

- ### Formulaire de connexion

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Email | email | email | ? | ?35? | Oui | Email... | ---- |
    | Mdp | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
    | Resté connecté | radio | autoLog | ---- | ---- | Non | ---- | ---- |
    Ainsi qu'un lien "mot de passe oublié".
  </details>

- ### Formulaire de modification des identifiants

  <details>

    *Pour changer ses identifiants il sera nécessaire d'entrer son mot de passe actuel.*

    *Pour changer de mot de passe il faudra entrer le nouveau mdp dans les champs prévus, **et** entrer son mot de passe actuel.*

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Pseudo | text | pseudo | ? | ? | Oui | Pseudo... | Pseudo actuel |
    | Email | email | email | ? | ?35? | Oui | Email... | Email actuelle |
    | Mdp actuel | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
    | Nouveau mdp | password | newPassword | 8 | ?50? | Non | Nouveau mot de passe... | ---- |
    | Mdp confirm | password | newPasswordConfirm | 8 | ?50? | Non | Confirmation... | ---- |
  </details>

- ### Page de profil

  <details>

    Page de profil du membre concerné, apparaitront sur cette page :
    - Les photo de profil et de couverture (bannière),
    - Les date d'inscription et de dernière connexion,
    - La bio,
    - Une série d'informations relatives à l'utilisateur qui pourront individuellement être rendues publique ou privée en modifiant le profil tel que : la date d'anniversaire, le sexe, la liste d'oeuvres suivies, l'avancement de visionnage/lecture de ces oeuvres, le watchtime, la liste d'amis etc.
    - Si l'utilisateur visionnant la page est **connecté**, on affichera un bouton de demande en ami,
    - [Si le **membre concerné** est connecté, on affichera les boutons de modification des identifiants et de suppression du compte],
    - [Si un **admin** est connecté, on affichera les boutons de gestion de l'utilisateur (reset des images et de la bio, modification du rôle etc.)]
  </details>

- ### Formulaire de modification de profil

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value | Checked |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Pdp | file (image) | pp | ---- | ---- | Non | ---- | ---- | ---- |
    | Bannière | file (image) | banner | ---- | ---- | Non | ---- | ---- | ---- |
    | Bio | textarea | bio | ---- | ---- | Non | ---- | ---- | ---- |
    | Infos extra | radio | \<info\> | ---- | ---- | Oui | ---- | show/hide | Valeur actuelle |
  </details>

- ### Page de chat privé/liste d'amis (style twitch)

  <details>
    Fraction de page qui prendra surement la forme d'une petite boite de dialogue avec un champ de texte.
  </details>

- ### Page de gestion des bans

  <details>
  
    [Nécessité d'être **admin** pour charger cette page]

    Formulaire d'ajout d'un ban :
    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Email | email | email | ? | ? | Oui | Email à ban... | ---- |
    | Raison | text | reason | ? | ? | Non | Raison du ban | ---- |

    Liste des bans : email, bourreau (sous forme de lien), message de ban (s'il y en a un), bouton unban
  </details>

## **Espace plateforme de visionnage/lecture**

- ### Formulaire d'ajout d'une plateforme

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de la plateforme... | ---- |
    | Type | checkbox | type | ---- | ---- | Oui | ---- | ---- |
    | Site web de la plateforme | text | websiteLink | ? | ? | Non | Lien vers la plateforme | ---- |
  </details>

- ### Formulaire de modification d'une plateforme

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de la plateforme... | Nom actuel |
    | Type | checkbox | type | ---- | ---- | Oui | ---- | Type actuel |
    | Site web de la plateforme | text | websiteLink | ? | ? | Non | Lien vers la plateforme | Lien actuel |
  </details>

- ### Page de la plateforme

  <details>

    Page type "profil" de la plateforme légale avec son nom, son type (anime et/ou manga) et une liste des oeuvres trouvables sous forme de lien à la page dédié de la plateforme.

    [Si **admin connecté** : on ajoute la possibilité de modifier la page de la plateforme],
  </details>

## **Espace oeuvre**

- ### Formulaire d'ajout d'une oeuvre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'oeuvre... | ---- |
    | Résumé | textarea | summary | ? | ? | Non | Résumé de l'oeuvre | ---- |
    | Auteur | text | author | ? | ? | Oui | Auteur... | ---- |
  </details>

- ### Formulaire de modification d'une oeuvre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'oeuvre... | Nom actuel |
    | Résumé | textarea | summary | ? | ? | Non | Résumé de l'oeuvre | Résumé actuel |
    | Auteur | text | author | ? | ? | Oui | Auteur... | Auteur actuel |
  </details>

- ### Formulaire d'ajout d'un anime à une oeuvre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'anime... | ---- |
    | Editeur | text | editor | ? | ? | Non | Editeur... | ---- |
    | Plateforme | text | streamingLink | ? | ? | Non | Lien... | ---- |
    | Durée des ep | number | epDuration | ? | ? | Non | Durée... | 24 |
  </details>

- ### Formulaire de modification d'un anime

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'anime... | Nom actuel |
    | Editeur | text | editor | ? | ? | Non | Editeur... | Editeur actuel |
    | Plateforme | text | streamingLink | ? | ? | Non | Lien... | Lien actuel |
    | Durée des ep | number | epDuration | ? | ? | Non | Durée... | Durée actuelle |
  </details>

- ### Formulaire d'ajout d'un manga à une oeuvre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom du manga... | ---- |
    | Illustrateur | text | illustrator | ? | ? | Non | Illustrateur... | ---- |
    | Lien de lecture | text | streamingLink | ? | ? | Non | Lien... | ---- |
  </details>

- ### Formulaire de modification d'un manga

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom du manga... | Nom actuel |
    | Illustrateur | text | illustrator | ? | ? | Non | Illustrateur... | Illu. actuel |
    | Lien de lecture | text | streamingLink | ? | ? | Non | Lien... | Lien actuel |
  </details>

- ### Formulaire d'ajout d'une saison (anime et manga)

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de la saison... | ---- |
  </details>

- ### Formulaire de modification d'une saison (anime et manga)

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de la saison... | Nom actuel |
  </details>

- ### Formulaire d'ajout d'un ou plusieurs épisodes

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nombre d'ep ajoutés | number | numberOfEp | 1 | ? | Oui | Nombre d'ep ajoutés... | 1 |
    | Nom automatique | radio | autoName | ---- | ---- | Oui | ---- | true/false |
    A répéter n fois :
    | Nom | text | name | ? | ? | Oui | Nom de l'ep... | ---- |
    | Durée | number | duration | ? | ? | Oui | Durée de l'ep | nb courant |
    | Saison | select | season | 1 | ? | Oui | Saison n°... | Hors saison |

    Le nom automatique doit être sur true par défaut et la saisie des ep sur disabled et inversement en fonction du nom automatique.
  </details>

- ### Formulaire de modification d'un épisode

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'ep... | Nom actuel |
    | Durée | number | duration | ? | ? | Oui | Durée de l'ep | nb actuel |
    | Saison | select | season | 1 | ? | Oui | Saison n°... | s actuelle |
  </details>

- ### Formulaire d'ajout d'un ou plusieurs chapitres

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nombre de chp ajoutés | number | numberOfChp | 1 | ? | Oui | Nombre de chp ajoutés... | 1 |
    | Nom automatique | radio | autoName | ---- | ---- | Oui | ---- | true/false |
    A répéter n fois :
    | Nom | text | name | ? | ? | Oui | Nom du chp... | ---- |
    | Saison | select | season | 1 | ? | Oui | Saison n°... | Hors saison |

    Le nom automatique doit être sur true par défaut et la saisie des chp sur disabled et inversement en fonction du nom automatique.
  </details>

- ### Formulaire de modification d'un chapitre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Nom | text | name | ? | ? | Oui | Nom de l'ep... | Nom actuel |
    | Saison | select | season | 1 | ? | Oui | Saison n°... | s actuelle |
  </details>

- ### Formulaire d'ajout d'un post d'actualité à une oeuvre (anime/manga/merch)

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Titre | text | title | ? | ? | Oui | Titre... | ---- |
    | Message | textarea | message | ? | ? | Oui | Message... | ---- |
  </details>

- ### Formulaire de modification d'un post d'actualité à une oeuvre

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Titre | text | title | ? | ? | Oui | Titre... | Titre actuel |
    | Message | textarea | message | ? | ? | Oui | Message... | Message actuel |
  </details>

- ### Page d'oeuvre

  <details>

    Sur la page de "profil" de l'oeuvre on trouvera les élements :
    - Espace dédié anime/manga interchangeable
        - Possibilité de noter quels ep/chp ont été vus/lus,
        - Date du prochain ep/chp si connu et si toujours en cours,
    - Actu de l'oeuvre,
    - Note moyenne,
    - [Si **membre connecté** : on ajoute la possibilité d'ajouter ou de modifier la note du membre],
    - [Si **membre connecté** : on ajoute la possibilité de follow/unfollow l'oeuvre pour être notifié des annonces et prochaines sorties],
    - [Si **admin connecté** : on ajoute la possibilité de modifier la page de l'oeuvre],
    - [Si **admin connecté** : on ajoute la possibilité d'ajouter ou de modifier les animes/mangas/actu de l'oeuvre],
  </details>

## **Espace calendrier**

- ### Page du calendrier

  <details>

    Calendrier interactif des prochaines sorties d'animes/mangas avec la possibilité de filtrer par tags, par type (anime/manga) et de "follow/unfollow" une oeuvre pour la faire apparaitre ou non dans sa page de calendrier perso

    [Si **membre connecté** : on ajoute la possibilité d'ajouter ou de modifier un post-it],
  </details>

- ### Page du calendrier perso

  <details>

    Calendrier personnalisé de l'utilisateur **connecté** avec possibilité de filtrer/unfollow les post-it.
  </details>

- ### Formulaire d'ajout d'un post-it

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Oeuvre | select | artworkName | ---- | ---- | Oui | ---- | ---- |
    | Titre (ex: Ep1 : lolo est dans le train) | text | title | ? | ? | Oui | Intitulé... | ---- |
    | Date | date | publishedDate | ---- | ---- | Oui | ---- | ---- |
  </details>

- ### Formulaire de modification d'un post-it

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Oeuvre | select | artworkName | ---- | ---- | Oui | ---- | Oeuvre actuelle |
    | Titre | text | title | ? | ? | Oui | Intitulé... | Titre actuel |
    | Date | date | publishedDate | ---- | ---- | Oui | ---- | Date actuelle |
  </details>

## **Espaces commentaires** (oeuvre, différents animes, différents mangas, profil de membre, profil de plateforme)

- ### Formulaire d'ajout de commentaire

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Message | textarea | comment | ? | ? | Oui | Commentaire... | ---- |
  </details>
  
- ### Formulaire de modification d'un commentaire

  <details>

    | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
    | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
    | Message | textarea | comment | ? | ? | Oui | Commentaire... | Commentaire actuel |
  </details>

- ### Liste des commentaires

  <details>

    Liste d'un nombre donné de commentaires avec chargement automatique des commentaires suivants sur scroll :
    - Pseudo sous forme de lien au profil,
    - Commentaire,
    - [Si **membre concerné connecté** : Bouton de modification],
    - [Si **membre concerné *OU* admin connecté** : Bouton de suppression]
  </details>
  