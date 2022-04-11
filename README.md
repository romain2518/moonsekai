# Description technique des pages du sites

## **Pages communes**

- ### Home

- ### Recherche

  - Par oeuvre (filtres à définir),
  - Par membre (avec boutons de gestion admin si admin connecté),
  - Par plateforme de visionnage/lecture.

## **Espace membre**

- ### Formulaire d'inscription

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Pseudo | text | pseudo | ? | ? | Oui | Pseudo... | ---- |
  | Email | email | email | ? | ?35? | Oui | Email... | ---- |
  | Mdp | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
  | Mdp confirm | password | passwordConfirm | 8 | ?50? | Oui | Confirmation... | ---- |

- ### Formulaire de connexion

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Email | email | email | ? | ?35? | Oui | Email... | ---- |
  | Mdp | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
  | Resté connecté | radio | autoLog | ---- | ---- | Non | ---- | ---- |

- ### Formulaire de modification des identifiants

  *Pour changer ses identifiants il sera nécessaire d'entrer son mot de passe actuel.*
  *Pour changer de mot de passe il faudra entrer le nouveau mdp dans les champs prévus, **et** entrer son mot de passe actuel.*

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Pseudo | text | pseudo | ? | ? | Oui | Pseudo... | Pseudo actuel |
  | Email | email | email | ? | ?35? | Oui | Email... | Email actuelle |
  | Mdp actuel | password | password | 8 | ?50? | Oui | Mot de passe... | ---- |
  | Nouveau mdp | password | newPassword | 8 | ?50? | Non | Nouveau mot de passe... | ---- |
  | Mdp confirm | password | newPasswordConfirm | 8 | ?50? | Non | Confirmation... | ---- |

- ### Page de profil

  Page de profil du membre concerné, apparaitront sur cette page :
  - Les photo de profil et de couverture (bannière),
  - Les date d'inscription et de dernière connexion,
  - La bio
  - Une série d'informations relatives à l'utilisateur qui pourront individuellement être rendues publique ou privée en modifiant le profil tel que : la date d'anniversaire, le sexe, la liste d'oeuvres suivies, l'avancement de visionnage/lecture de ces oeuvres, le watchtime etc.
  - Si le **membre concerné** est connecté, on affichera les boutons de modification des identifiants et de suppression du compte.
  - Si un **admin** est connecté, on affichera les boutons de gestion de l'utilisateur (reset des images et de la bio, modification du rôle etc.)

- ### Formulaire de modification de profil

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value | Checked |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Pdp | file (image) | pp | ---- | ---- | Non | ---- | ---- | ---- |
  | Bannière | file (image) | banner | ---- | ---- | Non | ---- | ---- | ---- |
  | Bio | textarea | bio | ---- | ---- | Non | ---- | ---- | ---- |
  | Infos extra | radio | \<info\> | ---- | ---- | Oui | ---- | show/hide | Valeur actuelle |

- ### Page de gestion des bans

  Formulaire d'ajout d'un ban :
  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Email | email | email | ? | ? | Oui | Email à ban... | ---- |
  | Raison | text | reason | ? | ? | Non | Raison du ban | ---- |

  Liste des bans : email, bourreau (sous forme de lien), message de ban (s'il y en a un), bouton unban

## **Espace plateforme de visionnage/lecture**

- ### Formulaire d'ajout d'une plateforme

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Nom | text | name | ? | ? | Oui | Nom de la plateforme... | ---- |
  | Type | checkbox | type | ---- | ---- | Oui | ---- | ---- |
  | Site web de la plateforme | text | websiteLink | ? | ? | Non | Lien vers la plateforme | ---- |

- ### Formulaire de modification d'une plateforme

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Nom | text | name | ? | ? | Oui | Nom de la plateforme... | Nom actuel |
  | Type | checkbox | type | ---- | ---- | Oui | ---- | Type actuel |
  | Site web de la plateforme | text | websiteLink | ? | ? | Non | Lien vers la plateforme | Lien actuel |

- ### Page de la plateforme

  Page type "profil" de la plateforme légale avec son nom, son type (anime et/ou manga) et une liste des oeuvres trouvables sous forme de lien à la page dédié de la plateforme.

## **Espace calendrier**

- ### Page du calendrier

  Calendrier interactif des prochaines sorties d'animes/mangas avec la possibilité de filtrer par tags, par type (anime/manga) et de "follow/unfollow" une oeuvre pour la faire apparaitre ou non dans sa page de calendrier perso

- ### Page du calendrier perso

  Calendrier personnalisé de l'utilisateur **connecté** avec possibilité de filtrer/unfollow les post-it.

- ### Formulaire d'ajout d'un post-it

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Oeuvre | select | artworkName | ---- | ---- | Oui | ---- | ---- |
  | Titre (ex: Ep1 : lolo est dans le train) | text | title | ? | ? | Oui | Intitulé... | ---- |
  | Date | date | publishedDate | ---- | ---- | Oui | ---- | ---- |

- ### Formulaire de modification d'un post-it

  | Champ | Type | Name | minwidth | maxwidth | Obligatoire | Placeholder | value |
  | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |
  | Oeuvre | select | artworkName | ---- | ---- | Oui | ---- | Oeuvre actuelle |
  | Titre | text | title | ? | ? | Oui | Intitulé... | Titre actuel |
  | Date | date | publishedDate | ---- | ---- | Oui | ---- | Date actuelle |
