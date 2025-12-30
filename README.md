<p align="center">
	<picture>
	    <source media="(prefers-color-scheme: dark)" srcset="https://github.com/user-attachments/assets/db787f1c-51be-43bf-989b-b10b5f9b5a2f">
	    <source media="(prefers-color-scheme: light)" srcset="https://github.com/user-attachments/assets/599489a7-28bd-4373-bb4c-eed06254fe67">
	    <img alt="Logo" src="https://github.com/user-attachments/assets/599489a7-28bd-4373-bb4c-eed06254fe67" style="width: 180px">
	</picture>
</p>

<h1 align="center">Dziennik Azayaka</h1>

<p align="center">
	Projekt otwartoźrodłowego dziennika elektronicznego dla szkół podstawowych i ponadpodstawowych.
</p>

## Funkcjonalność

- [x] Podstawowa funkcjonalność kont
- [ ] Podstawowe opisywanie organizacji szkoły w panelu administratora (WIP)
- [ ] Podstawowa funkcjonalność modułu sekretariatu (zarządzanie uczniami w oddziałach, księga uczniów/słuchaczy, księga
  ewidencji dzieci)![Up<?xml version="1.0" encoding="UTF-8" standalone="no"?>
- [ ] Konfiguracja dzienników lekcyjnych (oddziałów)
- [ ] Dokumentowanie realizacji zajęć
- [ ] Frekwencja na zajęciach
- [ ] Oceny

## Rozpoczynanie pracy

### Wymagania

|            | Minimalna wersja | Zalecana wersja |
|------------|------------------|-----------------|
| PHP        | 8.2+             | 8.4+            |
| Node.js	   | 22+              | 24+             |
| PostgreSQL | 10.0+            | 10.0+           |
| Composer   | 2.0+             | 2.0+            |
| pnpm       | 10.x             | 10.x            |

### Instalacja

1. Klonowanie repozytorium

```shell
git clone https://github.com/Dziennik-Azayaka/azayaka.git
```

2. Instalacja zależności

```shell
composer install
pnpm install
```

### Konfiguracja środowiska

Skopiuj plik `.env.example` do `.env` i zmodyfikuj go do własnych potrzeb.

### Generowanie klucza

```shell
php artisan key:generate
```

### Baza danych

1. Uruchamianie migracji

```shell
php artisan migrate
```

2. Dodanie przykadowych danych (opcjonalne)

```shell
php artisan db:seed
```

### Uruchamianie lokalnie

Uruchom oba skrypty:

```shell
pnpm dev
```

```shell
php artisan serve
```

## Licencja

Ten projekt udostępniany jest na licencji **GNU Affero General Public License** - szczegóły w
pliku [LICENSE](./LICENCE).
