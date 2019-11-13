#!/bin/bash
mkdir tmp
for file in *.md docs/fr_FR/*.md;
do
  if [ $file = "docs/fr_FR/index-ExtraTemplate.md" ] || [ $file = "docs/fr_FR/index.md" ]
  then
    echo "skip "$file
  else
    echo "process "$file
    cat $file | aspell --personal=./tests/tools/.aspell.fr.pws --lang=fr --encoding=utf-8 list | sort -u;
    cat $file | aspell --personal=./tests/tools/.aspell.fr.pws --lang=fr --encoding=utf-8 list >>tmp/list_mot.txt
  fi
done
if [ -e tmp/list_mot.txt ]
then
  rm tmp/list_mot.txt
  echo ------------------------
  echo Vocabulaire non Francais voir au dessus
  echo Ajout les mots a exclure du controle dans tests/tools/.aspell.fr.pws
  exit 1
fi
