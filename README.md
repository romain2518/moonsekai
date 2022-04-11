# Description technique des pages du sites

## **Pages communes**

- ### Home

- ### Recherche

  - Par oeuvre (filtres à définir),
  - Par membre,
  - Par Plateforme de visionnage/lecture.

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

- ### Page de modification de profil

- ### Page de gestion des bans

## **Espace plateforme**

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
