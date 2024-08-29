
# Serwis Ogłoszeniowy

Projekt ten został stworzony w ramach zajęć _system interakcyjny — projekt_. Aplikacja została zbudowany z użyciem https://bitbucket.org/tchojna/docker-symfony-starter-kit.


## Wymagania projektu zaliczeniowego

**Serwis ogłoszeniowy**

 - dostateczny (6/6)
   - ✅ użytkownicy: administrator mający możliwość tworzenia, edycji i usuwania treści na stronie oraz użytkownik niezalogowany, mający możliwość anonimowego dodawania ogłoszeń,
     - Administrator o danych logowania {email: `admin@example.com`, hasło: `admin1234`}
     - Kiedy użytkownik niezalogowany dodaje ogłoszenie, te ogłoszenie zostaje dodane pod kontem użytkownika niezalogowanego, o danych logowania {e-mail: `anon@example.com`, hasło: `anon1234`}
   - ✅ CRUD dla ogłoszeń (użytkownik niezalogowany ma możliwość tworzenia nowych ogłoszeń, o ich aktywacji i pojawieniu się na stronie decyduje administrator),
     - Ogłoszenia dodawane przez użytkownika niezalogowanego widoczne są dla administratora, na liście pod adresem `/task/task` widocznej tylko jeśli jesteśmy zalogowani jako administrator
     - Ogłoszenia zaakceptowane przez administratora lub dodane przez użytkowników zalogowanych są widoczne na liście pod adresem `/task/accepted`
     - (C) Akcja tworzenia, dostępna pod adresem `/task/create`
     - (R) Akcja czytania, dostępna pod adresem `/task/{id}`
     - (U) Akcja aktualizacji, dostępna pod adresem `/task/{id}/edit`
     - (D) Akcja usunięcia, dostępna pod adresem `/task/{id}/delete`
     - Administrator ma możliwość zaakceptowania danego ogłoszenia poprzez odpowiedni link pojawiający się tylko dla administratora w `/task/{id}`
   - ✅ CRUD dla kategorii, łączenie kategorii z ogłoszeniami (użytkownik niezalogowany ma wyłącznie możliwość dodania ogłoszenia do istniejących kategorii),
     - Kategorie połączone są z ogłoszeniami za pomocą pola relacji, podczas tworzenia ogłoszenia dodajemy je do danej kategorii 
     - (C) Akcja tworzenia, dostępna pod adresem `/category/create` 
     - (R) Akcja czytania, prowadzi nas do listy ogłoszeń dla danej kategorii, dostępna pod adresem `/task/category/{id}` 
     - (U) Akcja aktualizacji, dostępna pod adresem `/category/{id}/edit` 
     - (D) Akcja usuniecia, dostępna pod adresem `/category/{id}/delete`
   - ✅ wyświetlanie listy ogłoszeń dla danej kategorii, 
     - Dostępna pod adresem `/task/category/{id}` 
   - ✅ lista postów od najnowszego do najstarszego z paginacją po 10 rekordów na stronie,
     - Dla postów zaakceptowanych `/task/accepted`
     - Dla postów niezaakceptowanych `/task/task`
   - ✅ administrator (logowanie, zmiana hasła, zmiana danych administratora),
     - Logowanie działą poprzez `/login`
     - Zmiana danych działa poprzez `/acc/change`
 - dobry (5/6)
   - ✅ rejestracja i logowanie użytkowników,
     - Rejestracja możliwa poprzez `/acc/register`
     - Logowanie możliwe poprzez `/login`
   - ✅ zarządzanie kontem użytkownika (zmiana danych, zmiana hasła),
     - Zmiana danych (w tym hasła) użytkownika możliwa pod adresem `/acc/change`
   - ✅ każdy użytkownik zarządza tylko swoimi ogłoszeniami (każda zmiana ogłoszenia wymaga aktywacji administratora),
     - Edycja i usuwanie treści możliwe są tylko dla oryginalnego twórcy ogłoszenia (lub administratora)
   - ✅ zarządzanie kontami użytkowników przez administratora (zmiana hasła, zmiana danych),
     - Administrator ma dostęp do listy `/acc/list` gdzie znajdują się wszyscy użytkownicy, ma on wtedy możliwość do zmiany danych użytkownika (trzeba tylko wpisać w pole "obecne hasło" dowolne hasło, nie ma to znaczenia i tak dane zostaną zmienione na "nowy e-mail" i "nowe hasło")
     - Do zmienienia danych użytkownika przez administratora wykorzystywana jest ta sama formułka pod adresem `acc/change`, z jedyną różnicą że podawany jest id użytkownika w request
   - ✅ CRUD dla tagów, tagowanie ogłoszeń,
     - Lista tagów znajduje się na `/tag`
     - (C) Akcja tworzenia, dostępna pod adresem `/tag/create`
     - (R) Akcja czytania, dostępna pod adresem `/tag/{id}`
     - (U) Akcja aktualizacji, dostępna pod adresem `/tag/{id}/edit`
     - (D) Akcja usunięcia, doestępna pod adresem `/tag/{id}/delete`
   - 📌 filtrowanie listy ogłoszeń względem tagów,
 - bardzo dobry
   - 📌 zmiana uprawnień użytkowników (zmiana na administratora, odbieranie uprawnień, brak możliwości odebrania uprawnień ostatniemu administratorowi),
   - 📌 blokowanie kont użytkowników,
   - 📌 tworzenie ogłoszeń w formacie Markdown,
   - 📌 dodawanie plików graficznych do ogłoszeń,
   - 📌 komentowanie profili użytkowników przez innych zalogowanych użytkowników (ocena wiarygodności ogłoszeniodawcy)
   - 📌 wyszukiwarka ogłoszeń,
   - 📌 dodatkowe kryteria wyszukiwania, np. lokalizacja,