# Swiper CPT Carousel

Konfigurowalne karuzele Swiper.js dla WordPress z obsługą dowolnych Custom Post Types i pól ACF.

## Wymagania

- WordPress 6.0+
- PHP 8.0+
- Advanced Custom Fields (opcjonalnie, dla obsługi ACF)

## Instalacja

1. Wgraj folder `swiper-cpt-carousel` do katalogu `/wp-content/plugins/`
2. Aktywuj plugin w panelu WordPress → Wtyczki
3. W menu pojawi się pozycja **Karuzele** (ikona slajdów)

## Użycie

### 1. Tworzenie karuzeli

1. Przejdź do **Karuzele → Dodaj nową**
2. Nadaj karuzelę nazwę (widoczna tylko w BO)
3. Skonfiguruj ustawienia:

#### Wyświetlanie
- **Liczba slajdów** – 1, 2 lub 3 widocznych naraz (responsywne)
- **Maksymalna liczba postów** – ile postów załadować łącznie
- **Autoodtwarzanie** – automatyczne przesuwanie co 4 sekundy

#### Źródło postów
- **Typ posta** – wybierz dowolny CPT (Post, Strona, lub niestandardowy)
- **Filtruj po taksonomii** – pojawia się po wybraniu CPT; wybierz taksonomię
- **Kategorie** – zaznacz konkretne terminy lub zostaw „Wszystkie"

#### Dodatkowe pola ACF
- Plugin automatycznie wykrywa pola ACF przypisane do wybranego CPT
- Możesz wybrać pole z listy (przycisk **+ Dodaj z listy**)
- Lub wpisać klucz pola i etykietę ręcznie (przycisk **+ Dodaj ręcznie**)
- Kolejność pól = kolejność wyświetlania na slajdzie

### 2. Shortcode

Po zapisaniu karuzeli, w bocznym panelu „Shortcode" zobaczysz:

```
[karuzela id="123"]
```

Wklej go w dowolnym miejscu: edytor Gutenberg (blok Shortcode), klasyczny edytor, widget, plik szablonu:

```php
<?php echo do_shortcode('[karuzela id="123"]'); ?>
```

### 3. Lista karuzel w BO

W widoku listy (`Karuzele → Wszystkie karuzele`) widoczne są kolumny:
- **Shortcode** – kliknij, aby skopiować do schowka
- **Typ posta** – skonfigurowany CPT
- **Slajdy** – liczba slajdów widocznych naraz

## Personalizacja wyglądu

Wszystkie style korzystają ze zmiennych CSS. Nadpisz je w swoim motywie:

```css
:root {
    --scc-accent:      #e11d48;    /* kolor akcentu (przyciski, aktywna paginacja) */
    --scc-radius:      8px;        /* zaokrąglenie kart */
    --scc-img-ratio:   75%;        /* proporcje zdjęcia (75% = 4:3, 56.25% = 16:9) */
    --scc-bg-card:     #ffffff;    /* tło kart */
    --scc-shadow:      none;       /* cień kart */
}
```

## Struktura plików

```
swiper-cpt-carousel/
├── swiper-cpt-carousel.php   # Główny plik pluginu
├── admin/
│   ├── class-scc-admin.php   # Kolumny listy BO
│   ├── admin.css
│   └── admin.js
├── includes/
│   ├── class-scc-post-type.php  # Rejestracja CPT
│   ├── class-scc-metaboxes.php  # Pola metabox + AJAX helpers
│   ├── class-scc-shortcode.php  # Shortcode + enqueue
│   └── class-scc-ajax.php       # AJAX endpointy
├── templates/
│   ├── metabox-settings.php  # Widok ustawień w BO
│   └── carousel.php          # Widok frontu
└── public/
    ├── css/carousel.css
    ├── js/carousel.js
    └── img/placeholder.svg
```

## Swiper.js

Plugin ładuje Swiper 11 z CDN jsDelivr. Jeśli wolisz wersję lokalną, pobierz pliki ze [swiper.js](https://swiperjs.com/) i zmień ścieżki w `class-scc-shortcode.php`.

## Licencja

GPLv2 or later.
