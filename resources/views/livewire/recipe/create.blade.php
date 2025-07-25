<div>
    <form wire:submit="createRecipe" class="space-y-6">
        <div>
            <flux:heading size="lg">Ajouter une nouvelle recette</flux:heading>
            <flux:subheading>Créez une nouvelle recette pour ce projet.</flux:subheading>
        </div>

        <flux:field>
            <flux:label badge="Requis">Titre de la recette</flux:label>
            <flux:input
                wire:model="title"
                placeholder="Entrez le titre de la recette"
                required
            />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label>Description</flux:label>
            <flux:textarea
                wire:model="description"
                placeholder="Décrivez le problème constaté"
            />
            <flux:error name="description" />
        </flux:field>

        <flux:field>
            <flux:label>Type de recette</flux:label>
            <flux:select wire:model="type" placeholder="Choisissez le type">
                @foreach(\App\Enums\RecipeType::cases() as $type)
                    <flux:select.option
                        value="{{ $type->value }}"
                        label="{{ $type->label() }}"
                    />
                @endforeach
            </flux:select>
            <flux:error name="type" />
        </flux:field>

        <flux:field>
            <flux:label>Fichier (optionnel)</flux:label>
            <flux:input type="file" wire:model="recipeFile" />
            <flux:error name="recipeFile" />
        </flux:field>

        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <flux:modal.close>
                <flux:button variant="filled">Annuler</flux:button>
            </flux:modal.close>

            <flux:button type="submit" icon="plus" variant="primary">
                Créer la recette
            </flux:button>
        </div>
    </form>
</div>
