#!/bin/bash
mkdir tmp
for file in *.md docs/fr_FR/*.md;
  do
  echo "process "$file
  cat $file | aspell --personal=./tests/tools/.aspell.fr.pws --lang=fr --encoding=utf-8 list | sort -u;
  cat $file | aspell --personal=./tests/tools/.aspell.fr.pws --lang=fr --encoding=utf-8 list >>tmp/list_mot.txt
done
if [ -e tmp/list_mot.txt ]
then
  rm tmp/list_mot.txt
  echo Vocabulaire non Francais
  exit 1
fi
