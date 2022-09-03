# Data

## User

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| pseudo | VARCHAR(30) | Index, not null | min: 3<br> max: 30<br> NotBlank |
| email | VARCHAR(180) | Not null, unique | min: 3<br> max: 180<br> EmailType<br> Notblank<br> Unique |
| roles | json | | |
| password | string | | |
| picture_path | VARCHAR(255) | Not null | Default `0.png` |
| banner_path | VARCHAR(255) | Not null | Default `0.png` |
| biography | text | Null | max: 1000 |
| is_account_confirmed | boolean | Not null | Default False |
| is_subscribed_newsletter | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Message

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id_sender | ENTITY | Foreign key | |
| user_id_receiver | ENTITY | Foreign key | |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| is_read | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Report

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| type | VARCHAR(60) | Not null | Choices |
| url | VARCHAR(255) | Not null | min: 12<br> max: 255 |
| is_processed | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Comment

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| parent | ENTITY | Foreign key, null | |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| target_table | VARCHAR(255) | Not null, index | |
| target_id | int | Unsigned, not null, index | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Ban

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| email | VARCHAR(180) | Not null, unique | min: 3<br> max: 180<br> EmailType<br> Notblank<br> Unique |
| message | VARCHAR(255) | Null | max: 255<br> Notblank |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Work

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Film

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Plateform

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Work news

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Manga

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Volume

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Chapter

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Anime

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Season

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |

## Episode

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| name | VARCHAR(50)

## Contact request

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| applicant_email | VARCHAR(180) | Not null | min: 3<br> max: 180<br> EmailType<br> Notblank |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| is_processed | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |
