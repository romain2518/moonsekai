# Data

## User

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| pseudo | VARCHAR(30) | Index, not null | min: 3<br> max: 30<br> NotBlank |
| email | VARCHAR(180) | Index, not null, unique | min: 3<br> max: 180<br> EmailType<br> Notblank<br> Unique |
| roles | json | | |
| password | string | | |
| picture_path | VARCHAR(255) | Not null | Default `0.png` |
| banner_path | VARCHAR(255) | Not null | Default `0.png` |
| biography | text | Null | max: 1000 |
| notification_setting | json | Not null | |
| is_notification_redirection_enabled | boolean | Not null | Default False |
| is_muted | boolean | Not null | Default False |
| is_verified | boolean | Not null | Default False |
| is_subscribed_newsletter | boolean | Index, not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Notification

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| title | VARCHAR(20) | Not null | min: 2<br> max: 20<br> Notblank |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| is_read | boolean | Not null | Default False |
| target_table | VARCHAR(255) | Null, index | |
| target_id | int | Unsigned, Null, index | |
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
| url | VARCHAR(255) | Not null | min: 12<br> max: 255<br> UrlType |
| is_processed | boolean | Not null | Default False |
| is_important | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Comment

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| comment_id | ENTITY | Foreign key, null | |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| target_table | VARCHAR(255) | Not null, index | |
| target_id | int | Unsigned, not null, index | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## News

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| title | VARCHAR(100) | Not null | min: 5<br> max: 100<br> Notblank |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| picture_path | VARCHAR(255) | Null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Ban

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| email | VARCHAR(180) | Not null, unique | min: 3<br> max: 180<br> EmailType<br> Notblank<br> Unique |
| message | VARCHAR(255) | Null | max: 255 |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Rate

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| target_table | VARCHAR(255) | Not null, index | |
| target_id | int | Unsigned, not null, index | |
| rate | int | Not null | min: 0<br> max: 5<br> Notblank |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Progress

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| progress | VARCHAR(20) | Not null | Notblank<br> Choices |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Work

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| name | VARCHAR(190) | Index, not null | min: 1<br> max: 190<br> Notblank |
| type | VARCHAR(20) | Index, not null | Notblank<br> Choices |
| native_country | VARCHAR(190) | Index, not null | Notblank<br> Choices |
| original_name | VARCHAR(190) | Index, null | max: 190 |
| alternative_name | json | Null | max: 255 |
| picture_path | VARCHAR(255) | Not null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Tag

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Movie

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| description | text | Null | max: 1000 |
| duration | int | Index, not null | min: 1<br> max: 1440<br> Notblank |
| animation_studio | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| release_year | datetime | Index, not null | min: 1900<br> max: année actuelle + 10<br> Notblank<br> DateType |
| picture_path | VARCHAR(255) | Not null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Light novel

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| description | text | Null | max: 1000 |
| author | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| editor | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| release_year | datetime | Index, not null | min: 1900<br> max: année actuelle + 10<br> Notblank<br> DateType |
| picture_path | VARCHAR(255) | Not null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Platform

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| url | VARCHAR(100) | Not null | min: 3<br> max: 100<br> Notblank<br> UrlType |
| picture_path | VARCHAR(255) | Null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Work news

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| title | VARCHAR(100) | Not null | min: 5<br> max: 100<br> Notblank |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| picture_path | VARCHAR(255) | Null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Manga

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| description | text | Null | max: 1000 |
| state | VARCHAR(20) | Index, not null | Notblank<br> Choices |
| release_regularity | VARCHAR(20) | Index, not null | Notblank<br> Choices |
| author | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| designer | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| editor | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| release_year | datetime | Index, not null | min: 1900<br> max: année actuelle + 10<br> Notblank<br> DateType |
| picture_path | VARCHAR(255) | Not null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Volume

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| manga_id | ENTITY | Foreign key | |
| number | VARCHAR(50) | Not null | min: 1<br> max: 50<br> NotBlank |
| name | VARCHAR(50) | Null | max: 50 |
| picture_path | VARCHAR(255) | Null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Chapter

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| volume_id | ENTITY, null | Foreign key | |
| number | VARCHAR(50) | Not null | min: 1<br> max: 50<br> NotBlank |
| name | VARCHAR(50) | Null | max: 50 |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Anime

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| work_id | ENTITY | Foreign key | |
| name | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| description | text | Null | max: 1000 |
| state | VARCHAR(20) | Index, not null | Notblank<br> Choices |
| author | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| animation_studio | VARCHAR(100) | Index, not null | min: 1<br> max: 100<br> Notblank |
| release_year | datetime | Index, not null | min: 1900<br> max: année actuelle + 10<br> Notblank<br> DateType |
| picture_path | VARCHAR(255) | Not null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Season

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| anime_id | ENTITY | Foreign key | |
| number | VARCHAR(50) | Not null | min: 1<br> max: 50<br> NotBlank |
| name | VARCHAR(50) | Null | max: 50 |
| picture_path | VARCHAR(255) | Null | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Episode

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| season_id | ENTITY, null | Foreign key | |
| number | VARCHAR(50) | Not null | min: 1<br> max: 50<br> NotBlank |
| name | VARCHAR(50) | Null | max: 50 |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Calendar event

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| user_id | ENTITY | Foreign key | |
| title | VARCHAR(100) | Not null | min: 3<br> max: 100<br> Notblank |
| start | datetime | Not null | Min: now |
| end | datetime | Not null | Min: now |
| target_table | VARCHAR(255) | Not null, index | |
| target_id | int | Unsigned, not null, index | |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Contact request

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| id | int | Primary key, unsigned, not null, auto_increment | |
| applicant_email | VARCHAR(180) | Not null | min: 3<br> max: 180<br> EmailType<br> Notblank |
| message | VARCHAR(255) | Not null | min: 5<br> max: 255<br> Notblank |
| is_important | boolean | Not null | Default False |
| is_processed | boolean | Not null | Default False |
| created_at | datetime | Not null | |
| updated_at | datetime | Null | |

## Work_tag

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| work_id | ENTITY | Primary key, index, unsigned, not null |
| tag_id | ENTITY | Primary key, index, unsigned, not null |

## Work_platform

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| work_id | ENTITY | Primary key, index, unsigned, not null |
| platform_id | ENTITY | Primary key, index, unsigned, not null |

## User_work

| Field | Type | Specificities | Constraints |
|--|--|--|--|
| user_id | ENTITY | Primary key, index, unsigned, not null |
| work_id | ENTITY | Primary key, index, unsigned, not null |
