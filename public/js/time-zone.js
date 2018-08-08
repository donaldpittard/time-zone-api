(function(){      
    $('#time-zone').autocomplete({
        source: function(request, response) {
            var term = request.term;

            $.post('/api/timezones', {
                'term': term
            }).done(function(suggestions){
                var searchResults = suggestions.map((suggestion) => {
                    return {
                        label: suggestion.abbreviation.toUpperCase() + ': ' + suggestion.name + ' ' + suggestion.comments,
                        value: suggestion.abbreviation.toUpperCase()
                    }
                });

                response(searchResults);
            });
        }
    });
}());