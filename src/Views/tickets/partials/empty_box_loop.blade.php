
                @for($i=$count; $i < 6; $i++)
                    @if($i == 0)
                        <div class="tickets-user-list-empty-box">
                            @if($count == 0)
                                <h4 class="tickets-empty-title" style="font-size: 20px; text-align: center; line-height: 100px; margin: 0;">Nenhum chamado encontrado.</h4>
                            @endif         
                        </div>
                    @else
                        <div class="tickets-user-list-empty-box"></div>
                    @endif
                @endfor